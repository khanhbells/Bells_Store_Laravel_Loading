<?php

namespace App\Services;

use App\Services\Interfaces\PostCatalogueServiceInterface;
use App\Services\BaseService;
use App\Repositories\Interfaces\PostCatalogueRepositoryInterface as PostCatalogueRepository;
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
 * Class PostCatalogueService
 * @package App\Services
 */
class PostCatalogueService extends BaseService implements PostCatalogueServiceInterface
{
    protected $postCatalogueRepository;
    protected $nestedset;
    protected $language;
    protected $routerRepository;
    protected $controllerName = 'PostCatalogueController';
    public function __construct(PostCatalogueRepository $postCatalogueRepository, Nestedsetbie $nestedset, RouterRepository $routerRepository)
    {
        $this->postCatalogueRepository = $postCatalogueRepository;
        $this->routerRepository = $routerRepository;
        // $this->nestedset = new Nestedsetbie([
        //     'table' => 'post_catalogues',
        //     'foreignkey' => 'post_catalogue_id',
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
        $postCatalogues = $this->postCatalogueRepository->pagination(
            $this->paginateselect(),
            $condition,
            $perPage,
            ['path' => 'post/catalogue/index'],
            [
                'post_catalogues.lft',
                'ASC'
            ],
            [
                [
                    'post_catalogue_language as tb2',
                    'tb2.post_catalogue_id',
                    '=',
                    'post_catalogues.id'
                ]
            ],
        );
        return $postCatalogues;
    }
    public function create(Request $request, $languageId)
    {
        DB::beginTransaction();
        try {
            $postCatalogue = $this->createCatalogue($request);
            if ($postCatalogue->id > 0) {
                $this->updateLanguageForCatalogue($postCatalogue, $request, $languageId);
                $this->createRouter($postCatalogue, $request, $this->controllerName, $languageId);
                $this->nestedset = new Nestedsetbie([
                    'table' => 'post_catalogues',
                    'foreignkey' => 'post_catalogue_id',
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
            $postCatalogue = $this->postCatalogueRepository->findById($id);
            $flag = $this->updateCatalogue($postCatalogue, $request);
            if ($flag) {
                $this->updateLanguageForCatalogue($postCatalogue, $request, $languageId);
                $this->updateRouter($postCatalogue, $request, $this->controllerName, $languageId);
                $this->nestedset = new Nestedsetbie([
                    'table' => 'post_catalogues',
                    'foreignkey' => 'post_catalogue_id',
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
            $postCatalogue = $this->postCatalogueRepository->forceDelete($id);
            $this->routerRepository->forceDeleteByCondition([
                ['module_id', '=', $id],
                ['controllers', '=', 'App\Http\Controller\Frontend\PostCatalogueController']
            ]);
            $this->nestedset = new Nestedsetbie([
                'table' => 'post_catalogues',
                'foreignkey' => 'post_catalogue_id',
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
        $postCatalogue = $this->postCatalogueRepository->create($payload);
        return $postCatalogue;
    }
    private function updateCatalogue($postCatalogue, $request)
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
        $flag = $this->postCatalogueRepository->update($postCatalogue->id, $payload);
        return $flag;
    }

    public function updateLanguageForCatalogue($postCatalogue, $request, $languageId)
    {
        $payload = $this->formatLanguagePayload($postCatalogue, $request, $languageId);
        $postCatalogue->languages()->detach([$languageId, $postCatalogue->id]);
        $language = $this->postCatalogueRepository->createPivot($postCatalogue, $payload, 'languages');
        return $language;
    }
    private function formatLanguagePayload($postCatalogue, $request, $languageId)
    {
        $payload = $request->only($this->payloadLanguage());
        $payload['canonical'] = Str::slug($payload['canonical']);
        $payload['language_id'] = $languageId;
        $payload['post_catalogue_id'] = $postCatalogue->id;
        return $payload;
    }
    private function paginateselect()
    {
        return ['post_catalogues.id', 'post_catalogues.publish', 'post_catalogues.image', 'post_catalogues.level', 'post_catalogues.order', 'tb2.name', 'tb2.canonical'];
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
