<?php

namespace App\Services;

use App\Services\Interfaces\ProductServiceInterface;
use App\Services\BaseService;
use App\Repositories\Interfaces\ProductRepositoryInterface as ProductRepository;
use App\Repositories\Interfaces\RouterRepositoryInterface as RouterRepository;
use App\Repositories\Interfaces\PromotionRepositoryInterface as PromotionRepository;
use App\Repositories\Interfaces\ProductVariantLanguageRepositoryInterface as ProductVariantLanguageRepository;
use App\Repositories\Interfaces\ProductVariantAttributeRepositoryInterface as ProductVariantAttributeRepository;
use App\Repositories\Interfaces\AttributeCatalogueRepositoryInterface as AttributeCatalogueRepository;
use App\Repositories\Interfaces\AttributeRepositoryInterface as AttributeRepository;
use App\Services\Interfaces\ProductCatalogueServiceInterface as ProductCatalogueService;
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
use Illuminate\Pagination\Paginator;
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
    protected $promotionRepository;
    protected $attributeCatalogueRepository;
    protected $attributeRepository;
    protected $productCatalogueService;
    public function __construct(
        ProductRepository $productRepository,
        RouterRepository $routerRepository,
        ProductVariantLanguageRepository $productVariantLanguageRepository,
        ProductVariantAttributeRepository $productVariantAttributeRepository,
        PromotionRepository $promotionRepository,
        AttributeCatalogueRepository $attributeCatalogueRepository,
        AttributeRepository $attributeRepository,
        ProductCatalogueService $productCatalogueService,
    ) {
        $this->productRepository = $productRepository;
        $this->routerRepository = $routerRepository;
        $this->controllerName = 'ProductController';
        $this->productVariantLanguageRepository = $productVariantLanguageRepository;
        $this->productVariantAttributeRepository = $productVariantAttributeRepository;
        $this->promotionRepository = $promotionRepository;
        $this->attributeCatalogueRepository = $attributeCatalogueRepository;
        $this->attributeRepository = $attributeRepository;
        $this->productCatalogueService = $productCatalogueService;
    }

    public function paginate($request, $languageId, $productCatalogue = null, $page = 1, $extend = [])
    {
        if (!is_null($productCatalogue)) {
            Paginator::currentPageResolver(function () use ($page) {
                return $page;
            });
        }
        $perPage = (!is_null($productCatalogue)) ? 20 : 15;
        $condition = [
            'keyword' => addslashes($request->input('keyword')),
            'publish' => $request->input('publish', -1),
            'product_catalogue_id' => $request->input('product_catalogue_id'),
            'where' => [
                ['tb2.language_id', '=', $languageId]
            ]
        ];
        $paginationConfig = [
            'path' => ($extend['path']) ?? 'product/index',
        ];
        $orderBy = ['products.id', 'DESC'];
        $relations = ['product_catalogues'];
        $rawQuery = $this->whereRaw($request, $languageId, $productCatalogue);
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
    private function whereRaw($request, $languageId, $productCatalogue = null)
    {
        $rawCondition = [];
        if ($request->integer('product_catalogue_id') > 0 || !is_null($productCatalogue)) {
            $catId = ($request->integer('product_catalogue_id') > 0) ? $request->integer('product_catalogue_id') : $productCatalogue->id;
            $rawCondition['whereRaw'] =  [
                [
                    'tb3.product_catalogue_id IN (
                        SELECT id
                        FROM product_catalogues
                        JOIN product_catalogue_language ON product_catalogues.id = product_catalogue_language.product_catalogue_id
                        WHERE lft >= (SELECT lft FROM product_catalogues as pc WHERE pc.id = ?)
                        AND rgt <= (SELECT rgt FROM product_catalogues as pc WHERE pc.id = ?)
                        AND product_catalogue_language.language_id = ' . $languageId . '
                    )',
                    [$catId, $catId]
                ]
            ];
        }
        return $rawCondition;
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
                $this->productCatalogueService->setAttribute($product);
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

                $vId = ($payload['productVariant']['id'][$key]) ?? '';
                $productVariantId = sortString($vId);
                $uuid = Uuid::uuid5(Uuid::NAMESPACE_DNS, $product->id . ', ' . $payload['productVariant']['id'][$key]);
                // Xử lý album, loại bỏ phần '/laravelversion1.com/public'
                $album = ($payload['variant']['album'][$key]) ?? '';
                $album = str_replace('/laravelversion1.com/public', '', $album); // Loại bỏ phần đường dẫn
                $variant[] = [
                    'uuid' => $uuid,
                    'code' => $productVariantId,
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

            $product = $this->uploadProduct($id, $request);
            if ($product) {
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
                $this->productCatalogueService->setAttribute($product);
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


        // dd($variantAttribute);
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
        $payload['attribute'] = $request->input('attribute');
        $payload['variant'] = $this->formatJson($request, 'variant');
        $payload['user_id'] = Auth::id();
        $product = $this->productRepository->create($payload);
        return $product;
    }



    public function uploadProduct($id, $request)
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
        return $this->productRepository->update($id, $payload);
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


    private function convert_price(string $price = '')
    {
        return str_replace('.', '', $price);
    }
    private function paginateselect()
    {
        return [
            'products.id',
            'products.publish',
            'products.image',
            'products.order',
            'products.price',
            'tb2.name',
            'tb2.canonical'
        ];
    }
    private function payload()
    {
        return ['follow', 'publish', 'image', 'album', 'price', 'made_in', 'code', 'product_catalogue_id', 'attributeCatalogue', 'attribute', 'variant'];
    }
    private function payloadLanguage()
    {
        return  ['name', 'description', 'content', 'meta_title', 'meta_keyword', 'meta_description', 'canonical'];
    }
    public function combineProductAndPromotion($productId = [], $products, $flag = false)
    {
        $promotions = $this->promotionRepository->findByProduct($productId);
        if ($promotions) {
            if ($flag == true) {
                foreach ($promotions as $key => $promotion) {
                    $products->promotions = $promotion;
                }
                return $products;
            }
            foreach ($products as $index => $product) {
                foreach ($promotions as $key => $promotion) {
                    if ($promotion->product_id === $product->id) {
                        $products[$index]->promotions = $promotion;
                    }
                }
            }
        }
        return $products;
    }

    public function getAttribute($product, $language)
    {
        if ($product->attribute != null) {
            $attributeCatalogueId = array_keys($product->attribute);
            $attrCatalogues = $this->attributeCatalogueRepository->getAttributeCatalogueWhereIn($attributeCatalogueId, 'attribute_catalogues.id', $language);
            //------
            $attributeId = array_merge(...$product->attribute);
            $attrs = $this->attributeRepository->findAttributeByIdArray($attributeId, $language);
            if (!is_null($attrCatalogues)) {
                foreach ($attrCatalogues as $key => $val) {
                    $tempAttributes = [];
                    foreach ($attrs as $attr) {
                        if ($val->id == $attr->attribute_catalogue_id) {
                            $tempAttributes[] = $attr;
                        }
                    }
                    $val->attributes = $tempAttributes;
                }
            }
            $product->attributeCatalogue = $attrCatalogues;
            return $product;
        }
        return $product;
    }

    public function filter($request)
    {
        $perPage = $request->input('perpage');
        $param['priceQuery'] = $this->priceQuery($request);
        $param['attributeQuery'] = $this->attributeQuery($request);
        $param['rateQuery'] = $this->rateQuery($request);
        $param['productCatalogueQuery'] = $this->productCatalogueQuery($request);
        $query = $this->combineFilterQuery($param);
        $products = $this->productRepository->filter($query, $perPage);
        $productId = $products->pluck('id')->toArray();
        if (count($productId) && !is_null($productId)) {
            $products = $this->combineProductAndPromotion($productId, $products);
        }
        return $products;
    }

    private function combineFilterQuery($param)
    {
        $query = [];
        foreach ($param as $array) {
            foreach ($array as $key => $value) {
                if (!isset($query[$key])) {
                    $query[$key] = [];
                }
                if (is_array($value)) {
                    $query[$key] = array_merge($query[$key], $value);
                } else {
                    $query[$key][] = $value;
                }
            }
        }
        return $query;
    }

    private function productCatalogueQuery($request)
    {

        $productCatalogueId = $request->input('productCatalogueId');
        $query['join'] = null;
        $query['whereRaw'] = null;
        if ($productCatalogueId > 0) {
            $query['join'] = [
                ['product_catalogue_product as pcp', 'pcp.product_id', '=', 'products.id']
            ];
            $query['whereRaw'] = [
                [
                    'pcp.product_catalogue_id IN (
                        SELECT id
                        FROM product_catalogues
                        WHERE lft >= (SELECT lft FROM product_catalogues as pc WHERE pc.id = ?)
                        AND rgt <= (SELECT rgt FROM product_catalogues as pc WHERE pc.id = ?)
                    )',
                    [$productCatalogueId, $productCatalogueId]
                ]
            ];
        }
        return $query;
    }

    private function rateQuery($request)
    {
        $rates = $request->input('rate');
        $query['join'] = null;
        $query['having'] = null;

        if (!is_null($rates) && count($rates)) {
            $query['join'] = [
                [
                    'reviews',
                    'reviews.reviewable_id',
                    '=',
                    'products.id'
                ]
            ];
            $rateCondition = [];
            $bindings = [];
            foreach ($rates as $rate) {
                if ($rate != 5) {
                    $minRate = $rate;
                    $maxRate = $rate . '.9';
                    $rateCondition[] = '(AVG(reviews.score) >= ? AND AVG(reviews.score) <= ?)';
                    $bindings[] = $minRate;
                    $bindings[] = $maxRate;
                } else {
                    $rateCondition[] = 'AVG(reviews.score) = ?';
                    $bindings[] = 5;
                }
            }
            $query['where'] = function ($query) {
                $query->where('reviews.reviewable_type', '=', 'App\\Models\\Product');
            };
            $query['having'] = function ($query) use ($rateCondition, $bindings) {
                $query->havingRaw(implode(' OR ', $rateCondition), $bindings);
            };
        }
        return $query;
    }

    private function attributeQuery($request)
    {
        $attributes = $request->input('attributes');
        $query['select'] = null;
        $query['join'] = null;
        $query['where'] = null;

        if (!is_null($attributes) && count($attributes)) {
            $query['select'][] = DB::raw('MAX(pv.price) as variant_price');
            $query['select'][] = DB::raw('MAX(pv.sku) as variant_sku');
            $query['join'] = [
                ['product_variants as pv', 'pv.product_id', '=', 'products.id']
            ];
            foreach ($attributes as $key => $attribute) {
                $joinKey = 'tb' . $key;
                $query['join'][] =
                    [
                        "product_variant_attribute as {$joinKey}",
                        "$joinKey.product_variant_id",
                        '=',
                        'pv.id'
                    ];
                $query['where'][] = function ($query) use ($joinKey, $attribute) {
                    foreach ($attribute as $attr) {
                        $query->orWhere("$joinKey.attribute_id", '=', $attr);
                    }
                };
            }
        }
        return $query;
    }

    private function priceQuery($request)
    {
        $price = $request->input('price');
        $priceMin = str_replace('đ', '', convert_price($price['price_min']));
        $priceMax = str_replace('đ', '', convert_price($price['price_max']));
        // $query['select'] = null;
        $query['join'] = null;
        $query['having'] = null;

        if ($priceMax > $priceMin) {
            $query['join'] = [
                ['promotion_product_variant as ppv', 'ppv.product_id', '=', 'products.id'],
                ['promotions', 'ppv.promotion_id', '=', 'promotions.id'],
            ];
            $query['select'] = DB::raw('MAX(products.price - 
            IF(promotions.maxDiscountValue != 0,
                LEAST(
                    CASE
                        WHEN discountType = "cash" THEN discountValue
                        WHEN discountType = "percent" THEN products.price * discountValue / 100
                        ELSE 0
                    END,
                    promotions.maxDiscountValue
                ),
                CASE
                    WHEN discountType = "cash" THEN discountValue
                    WHEN discountType = "percent" THEN products.price * discountValue / 100
                    ELSE 0
                END
            )
        ) as discounted_price');
            $query['having'] = function ($query) use ($priceMin, $priceMax) {
                $query->havingRaw('discounted_price >= ? AND discounted_price <= ?', [$priceMin, $priceMax]);
            };
        }
        return $query;
    }
}
