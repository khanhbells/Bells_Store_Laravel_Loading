<?php

namespace App\Services;

use App\Services\Interfaces\ProductServiceInterface;
use App\Services\BaseService;
use App\Repositories\Interfaces\ProductRepositoryInterface as ProductRepository;
use App\Repositories\Interfaces\RouterRepositoryInterface as RouterRepository;
use App\Repositories\Interfaces\ProductVariantLanguageRepositoryInterface as ProductVariantLanguageRepository;
use App\Repositories\Interfaces\ProductVariantAttributeRepositoryInterface as ProductVariantAttributeRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Helpers;
use App\Models\ProductVariant;
use Ramsey\Uuid\Uuid;

/**
 * Class ProductService
 * @package App\Services
 */
class ProductService extends BaseService implements ProductServiceInterface
{
    protected $productRepository;
    protected $routerRepository;
    protected $productVariantLanguageRepository;
    protected $productVariantAttributeRepository;
    public function __construct(
        ProductRepository $productRepository,
        RouterRepository $routerRepository,
        ProductVariantLanguageRepository $productVariantLanguageRepository,
        ProductVariantAttributeRepository $productVariantAttributeRepository
    ) {
        $this->productRepository = $productRepository;
        $this->routerRepository = $routerRepository;
        $this->controllerName = 'ProductController';
        $this->productVariantLanguageRepository = $productVariantLanguageRepository;
        $this->productVariantAttributeRepository = $productVariantAttributeRepository;
    }
    public function paginate($request, $languageId)
    {
        $perPage = $request->integer('perpage');
        $condition = [
            'keyword' => addslashes($request->input('keyword')),
            'publish' => $request->input('publish', -1),
            'product_catalogue_id' => $request->input('product_catalogue_id'),
            'where' => [
                ['tb2.language_id', '=', $languageId]
            ]
        ];
        $paginationConfig = [
            'path' => 'product/index'
        ];
        $orderBy = ['products.id', 'DESC'];
        $relations = ['product_catalogues'];
        $rawQuery = $this->whereRaw($request, $languageId);
        $joins = [
            ['product_language as tb2', 'tb2.product_id', '=', 'products.id'],
            ['product_catalogue_product as tb3', 'products.id', '=', 'tb3.product_id']
        ];
        $products = $this->productRepository->pagination(
            $this->paginateselect(),
            $condition,
            $perPage,
            $paginationConfig,
            $orderBy,
            $joins,
            $relations,
            $rawQuery
        );
        return $products;
    }
    public function create(Request $request, $languageId)
    {
        DB::beginTransaction();

        try {
            $product = $this->createProduct($request);
            if ($product->id > 0) {
                $this->uploadLanguageForProduct($product, $request, $languageId);
                $this->updateCatalogueForproduct($product, $request);
                $this->createRouter($product, $request, $this->controllerName, $languageId);

                if ($request->input('attribute')) {
                    $this->createVariant($product, $request, $languageId);
                }
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            // In ra lỗi để debug
            dd($e);
            // Ghi lỗi vào log
            Log::error($e->getMessage());
            // Trả về mã lỗi 500
            abort(500, 'Đã xảy ra lỗi trong quá trình tạo bản ghi.');
        }
    }


    private function createVariantLanguage() {}

    private function combineAttribute($attributes = [], $index = 0)
    {
        if ($index === count($attributes))
            return [[]];
        $subCombines = $this->combineAttribute($attributes, $index + 1);

        $combines = [];

        foreach ($attributes[$index] as $key => $val) {

            foreach ($subCombines as $keySub => $valSub) {
                $combines[] = array_merge([$val], $valSub);
            }
        }
        return $combines;
    }
    private function createVariantArray(array $payload = [], $product): array
    {
        $variant = [];
        if (isset($payload['variant']['sku']) && count($payload['variant']['sku'])) {
            foreach ($payload['variant']['sku'] as $key => $val) {
                $uuid = Uuid::uuid5(Uuid::NAMESPACE_DNS, $product->id . ', ' . $payload['productVariant']['id'][$key]);
                // Xử lý album, loại bỏ phần '/laravelversion1.com/public'
                $album = ($payload['variant']['album'][$key]) ?? '';
                $album = str_replace('/laravelversion1.com/public', '', $album); // Loại bỏ phần đường dẫn
                $variant[] = [
                    'uuid' => $uuid,
                    'code' => ($payload['productVariant']['id'][$key]) ?? '',
                    'quantity' => ($payload['variant']['quantity'][$key]) ?? '',
                    'sku' => $val,
                    'price' => ($payload['variant']['price'][$key]) ? $this->convert_price($payload['variant']['price'][$key]) : '',
                    'barcode' => ($payload['variant']['barcode'][$key]) ?? '',
                    'file_name' => ($payload['variant']['file_name'][$key]) ?? '',
                    'file_url' => ($payload['variant']['file_url'][$key]) ?? '',
                    'album' => $album,
                    'user_id' => Auth::id()
                ];
            }
        }
        return $variant;
    }

    // --------------------------------------------------------------------------------
    public function update($id, Request $request, $languageId)
    {
        DB::beginTransaction();
        try {

            $product = $this->productRepository->findById($id);
            if ($this->uploadProduct($product, $request)) {
                $this->uploadLanguageForProduct($product, $request, $languageId);
                $this->updateCatalogueForProduct($product, $request);
                $this->updateRouter($product, $request, $this->controllerName, $languageId);
                $product->product_variants()->each(function ($variant) {
                    $variant->languages()->detach();
                    $variant->attributes()->detach();
                    $variant->delete();
                });
                if ($request->input('attribute')) {
                    $this->createVariant($product, $request, $languageId);
                }
            }
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }

    private function createVariant($product, $request, $languageId)
    {
        $payload = $request->only(['variant', 'productVariant', 'attribute']);
        $variant = $this->createVariantArray($payload, $product);
        $variants = $product->product_variants()->createMany($variant);
        $variantId = $variants->pluck('id');
        $productVariantLanguage = [];
        $variantAttribute = [];
        $attributesCombines = $this->combineAttribute(array_values($payload['attribute']));
        if (count($variantId)) {
            foreach ($variantId as $key => $val) {
                $productVariantLanguage[] = [
                    'product_variant_id' => $val,
                    'language_id' => $languageId,
                    'name' => $payload['productVariant']['name'][$key]
                ];
                if (count($attributesCombines)) {
                    foreach ($attributesCombines[$key] as  $attributeId) {
                        $variantAttribute[] = [
                            'product_variant_id' => $val,
                            'attribute_id' => $attributeId
                        ];
                    }
                }
            }
        }
        $variantLanguage = $this->productVariantLanguageRepository->createBatch($productVariantLanguage);

        $variantAttribute = $this->productVariantAttributeRepository->createBatch($variantAttribute);


        // dd($attributesCombines);
    }


    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $product = $this->productRepository->forceDelete($id);
            $this->routerRepository->forceDeleteByCondition([
                ['module_id', '=', $id],
                ['controllers', '=', 'App\Http\Controller\Frontend\ProductController']
            ]);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return false;
        }
    }
    public function updateStatus($product = [])
    {
        DB::beginTransaction();
        try {
            $payload[$product['field']] = (($product['value'] == 1) ? 2 : 1);
            $product = $this->productRepository->update($product['modelId'], $payload);
            // $this->changeUserStatus($product, $payload[$product['field']]);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }
    public function updateStatusAll($product)
    {
        DB::beginTransaction();
        try {
            $payload[$product['field']] = $product['value'];
            $flag = $this->productRepository->updateByWhereIn('id', $product['id'], $payload);
            // $this->changeUserStatus($product, $product['value']);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }
    private function createProduct($request)
    {
        $payload = $request->only($this->payload());
        // Kiểm tra xem album có được truyền không
        if (isset($payload['album'])) {
            $payload['album'] = $this->formatAlbum($payload['album']);
        }
        // Kiểm tra xem image có được truyền không
        if (isset($payload['image'])) {
            $payload['image'] = $this->formatImage($payload['image']);
        }
        $payload['price'] = $this->convert_price(($payload['price']) ?? 0);
        $payload['attributeCatalogue'] = $this->formatJson($request, 'attributeCatalogue');
        $payload['attribute'] = $this->formatJson($request, 'attribute');
        $payload['variant'] = $this->formatJson($request, 'variant');
        $payload['user_id'] = Auth::id();
        $product = $this->productRepository->create($payload);
        return $product;
    }



    public function uploadProduct($product, $request)
    {
        $payload = $request->only($this->payload());
        $payload['price'] = str_replace('.', '', $payload['price']);
        if (isset($payload['album'])) {
            $payload['album'] = $this->formatAlbum($payload['album']);
        }
        if (isset($payload['image'])) {
            $payload['image'] = $this->formatImage($payload['image']);
        }
        $payload['price'] = $this->convert_price($payload['price']);
        // dd($payload);
        // Kiểm tra và xử lý cả hai trường hợp ảnh có tiền tố 'http://localhost:81/laravelversion1.com/public' hoặc '/laravelversion1.com/public'
        return $this->productRepository->update($product->id, $payload);
    }
    private function uploadLanguageForProduct($product, $request, $languageId)
    {
        $payload = $request->only($this->payloadLanguage());
        $payload = $this->formatLanguagePayload($payload, $product->id, $languageId);
        $product->languages()->detach([$languageId, $product->id]);
        return $this->productRepository->createPivot($product, $payload, 'languages');
    }
    private function formatLanguagePayload($payload, $productId, $languageId)
    {
        $payload['canonical'] = Str::slug($payload['canonical']);
        $payload['language_id'] = $languageId;
        $payload['product_id'] = $productId;
        return $payload;
    }
    private function updateCatalogueForproduct($product, $request)
    {
        $product->product_catalogues()->sync($this->catalogue($request));
    }
    private function catalogue($request)
    {
        if ($request->input('catalogue') != null) {
            return array_unique(array_merge($request->input('catalogue'), [$request->product_catalogue_id]));
        } else {
            return [$request->product_catalogue_id];
        }
    }
    private function whereRaw($request, $languageId)
    {
        $rawCondition = [];
        if ($request->integer('product_catalogue_id') > 0) {
            $rawCondition['whereRaw'] =  [
                [
                    'tb3.product_catalogue_id IN (
                        SELECT id
                        FROM product_catalogues
                        WHERE lft >= (SELECT lft FROM product_catalogues as pc WHERE pc.id = ?)
                        AND rgt <= (SELECT rgt FROM product_catalogues as pc WHERE pc.id =?)
                    )',
                    [$request->integer('product_catalogue_id'), $request->integer('product_catalogue_id')]
                ]
            ];
        }
        return $rawCondition;
    }
    private function convert_price(string $price = '')
    {
        return str_replace('.', '', $price);
    }
    private function paginateselect()
    {
        return ['products.id', 'products.publish', 'products.image', 'products.order', 'tb2.name', 'tb2.canonical'];
    }
    private function payload()
    {
        return ['follow', 'publish', 'image', 'album', 'price', 'made_in', 'code', 'product_catalogue_id', 'attributeCatalogue', 'attribute', 'variant'];
    }
    private function payloadLanguage()
    {
        return  ['name', 'description', 'content', 'meta_title', 'meta_keyword', 'meta_description', 'canonical'];
    }
}
