<?php

namespace App\Services;

use App\Services\Interfaces\ProductCatalogueServiceInterface;
use App\Services\BaseService;
use App\Repositories\Interfaces\ProductCatalogueRepositoryInterface as ProductCatalogueRepository;
use App\Repositories\Interfaces\RouterRepositoryInterface as RouterRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Classes\Nestedsetbie;
use Illuminate\Support\Str;

/**
 * Class ProductCatalogueService
 * @package App\Services
 */
class ProductCatalogueService extends BaseService implements ProductCatalogueServiceInterface
{
    protected $productCatalogueRepository;
    protected $nestedset;
    protected $language;
    protected $routerRepository;
    protected $controllerName = 'ProductCatalogueController';
    public function __construct(ProductCatalogueRepository $productCatalogueRepository, Nestedsetbie $nestedset, RouterRepository $routerRepository)
    {
        $this->productCatalogueRepository = $productCatalogueRepository;
        $this->routerRepository = $routerRepository;
        // $this->nestedset = new Nestedsetbie([
        //     'table' => 'product_catalogues',
        //     'foreignkey' => 'product_catalogue_id',
        //     'language_id' => 1,
        // ]);
    }

    public function paginate($request, $languageId)
    {
        $perPage = $request->integer('perpage');
        $condition = [
            'keyword' => addslashes($request->input('keyword')),
            'publish' => $request->input('publish', -1),
            'where' => [
                ['tb2.language_id', '=', $languageId]
            ]
        ];
        $productCatalogues = $this->productCatalogueRepository->pagination(
            $this->paginateselect(),
            $condition,
            $perPage,
            ['path' => 'product/catalogue/index'],
            [
                'product_catalogues.lft',
                'ASC'
            ],
            [
                [
                    'product_catalogue_language as tb2',
                    'tb2.product_catalogue_id',
                    '=',
                    'product_catalogues.id'
                ]
            ],
        );
        return $productCatalogues;
    }
    public function create(Request $request, $languageId)
    {
        DB::beginTransaction();
        try {
            $productCatalogue = $this->createCatalogue($request);
            if ($productCatalogue->id > 0) {
                $this->updateLanguageForCatalogue($productCatalogue, $request, $languageId);
                $this->createRouter($productCatalogue, $request, $this->controllerName, $languageId);
                $this->nestedset = new Nestedsetbie([
                    'table' => 'product_catalogues',
                    'foreignkey' => 'product_catalogue_id',
                    'language_id' => $languageId,
                ]);
                $this->nestedset();
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
            $productCatalogue = $this->productCatalogueRepository->findById($id);
            $flag = $this->updateCatalogue($productCatalogue, $request);
            if ($flag) {
                $this->updateLanguageForCatalogue($productCatalogue, $request, $languageId);
                $this->updateRouter($productCatalogue, $request, $this->controllerName, $languageId);
                $this->nestedset = new Nestedsetbie([
                    'table' => 'product_catalogues',
                    'foreignkey' => 'product_catalogue_id',
                    'language_id' => $languageId,
                ]);
                $this->nestedset();
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

    public function destroy($id, $languageId)
    {
        DB::beginTransaction();
        try {
            $productCatalogue = $this->productCatalogueRepository->forceDelete($id);
            $this->nestedset = new Nestedsetbie([
                'table' => 'product_catalogues',
                'foreignkey' => 'product_catalogue_id',
                'language_id' => $languageId,
            ]);
            $this->nestedset();
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }
    public function updateStatus($product = [])
    {
        DB::beginTransaction();
        try {
            $payload[$product['field']] = (($product['value'] == 1) ? 2 : 1);
            $productCatalogue = $this->productCatalogueRepository->update($product['modelId'], $payload);
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
            $flag = $this->productCatalogueRepository->updateByWhereIn('id', $product['id'], $payload);
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

    private function createCatalogue($request)
    {
        $payload = $request->only($this->payload());
        // Xử lý album - loại bỏ '/laravelversion1.com/public' khỏi các đường dẫn
        if (isset($payload['album'])) {
            $payload['album'] = $this->formatAlbum($payload['album']);
        }
        // Kiểm tra xem image có được truyền không
        if (isset($payload['image'])) {
            $payload['image'] = $this->formatImage($payload['image']);
        }
        $payload['user_id'] = Auth::id();

        // Tạo bản ghi
        $productCatalogue = $this->productCatalogueRepository->create($payload);
        return $productCatalogue;
    }
    private function updateCatalogue($productCatalogue, $request)
    {
        $payload = $request->only($this->payload());
        // Xử lý album - loại bỏ '/laravelversion1.com/public' khỏi các đường dẫn
        if (isset($payload['album'])) {
            $payload['album'] = $this->formatAlbum($payload['album']);
        }
        // Kiểm tra xem image có được truyền không
        if (isset($payload['image'])) {
            $payload['image'] = $this->formatImage($payload['image']);
        }
        $flag = $this->productCatalogueRepository->update($productCatalogue->id, $payload);
        return $flag;
    }

    public function updateLanguageForCatalogue($productCatalogue, $request, $languageId)
    {
        $payload = $this->formatLanguagePayload($productCatalogue, $request, $languageId);
        $productCatalogue->languages()->detach([$languageId, $productCatalogue->id]);
        $language = $this->productCatalogueRepository->createPivot($productCatalogue, $payload, 'languages');
        return $language;
    }
    private function formatLanguagePayload($productCatalogue, $request, $languageId)
    {
        $payload = $request->only($this->payloadLanguage());
        $payload['canonical'] = Str::slug($payload['canonical']);
        $payload['language_id'] = $languageId;
        $payload['product_catalogue_id'] = $productCatalogue->id;
        return $payload;
    }
    private function paginateselect()
    {
        return ['product_catalogues.id', 'product_catalogues.publish', 'product_catalogues.image', 'product_catalogues.level', 'product_catalogues.order', 'tb2.name', 'tb2.canonical'];
    }
    private function payload()
    {
        return ['parent_id', 'follow', 'publish', 'image', 'album'];
    }
    private function payloadLanguage()
    {
        return  ['name', 'description', 'content', 'meta_title', 'meta_keyword', 'meta_description', 'canonical'];
    }
}
