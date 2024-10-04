<?php

namespace App\Services;

use App\Services\Interfaces\ProductServiceInterface;
use App\Services\BaseService;
use App\Repositories\Interfaces\ProductRepositoryInterface as ProductRepository;
use App\Repositories\Interfaces\RouterRepositoryInterface as RouterRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Class ProductService
 * @package App\Services
 */
class ProductService extends BaseService implements ProductServiceInterface
{
    protected $productRepository;
    protected $routerRepository;
    public function __construct(ProductRepository $productRepository, RouterRepository $routerRepository)
    {
        $this->productRepository = $productRepository;
        $this->routerRepository = $routerRepository;
        $this->controllerName = 'ProductController';
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
            // dd($request);
            $product = $this->createProduct($request);
            if ($product->id > 0) {
                $this->uploadLanguageForProduct($product, $request, $languageId);
                $this->updateCatalogueForproduct($product, $request);
                $this->createRouter($product, $request, $this->controllerName, $languageId);
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
    // --------------------------------------------------------------------------------
    public function update($id, Request $request, $languageId)
    {
        DB::beginTransaction();
        try {
            $product = $this->productRepository->findById($id);
            // dd($product);
            if ($this->uploadProduct($product, $request)) {
                $this->uploadLanguageForProduct($product, $request, $languageId);
                $this->updateCatalogueForProduct($product, $request);
                $this->updateRouter($product, $request, $this->controllerName, $languageId);
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

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $product = $this->productRepository->forceDelete($id);
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
        // dd($payload['album'], $payload['image']);
        $payload['user_id'] = Auth::id();
        // Tạo bản ghi
        $product = $this->productRepository->create($payload);
        return $product;
    }



    private function uploadProduct($product, $request)
    {
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($payload['album']);
        $payload['image'] = $this->formatImage($payload['image']);
        // dd($payload);
        // Kiểm tra và xử lý cả hai trường hợp ảnh có tiền tố 'http://localhost:81/laravelversion1.com/public' hoặc '/laravelversion1.com/public'

        return $this->productRepository->update($product->id, $payload);
    }
    private function uploadLanguageForProduct($product, $request, $languageId)
    {
        $payload = $request->only($this->payloadLanguage());
        $payload = $this->formatLanguagePayload($payload, $product->id, $languageId);
        $product->languages()->detach([$this->language, $product->id]);
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
    private function paginateselect()
    {
        return ['products.id', 'products.publish', 'products.image', 'products.order', 'tb2.name', 'tb2.canonical'];
    }
    private function payload()
    {
        return ['follow', 'publish', 'image', 'album', 'product_catalogue_id'];
    }
    private function payloadLanguage()
    {
        return  ['name', 'description', 'content', 'meta_title', 'meta_keyword', 'meta_description', 'canonical'];
    }
}
