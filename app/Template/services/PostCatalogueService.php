<?php

namespace App\Services;

use App\Services\Interfaces\{class}CatalogueServiceInterface;
use App\Services\BaseService;
use App\Repositories\Interfaces\{class}CatalogueRepositoryInterface as {class}CatalogueRepository;
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
 * Class {class}CatalogueService
 * @package App\Services
 */
class {class}CatalogueService extends BaseService implements {class}CatalogueServiceInterface
{
    protected ${module}CatalogueRepository;
    protected $nestedset;
    protected $language;
    protected $routerRepository;
    protected $controllerName = '{class}CatalogueController';
    public function __construct({class}CatalogueRepository ${module}CatalogueRepository, Nestedsetbie $nestedset, RouterRepository $routerRepository)
    {
        $this->{module}CatalogueRepository = ${module}CatalogueRepository;
        $this->routerRepository = $routerRepository;
        // $this->nestedset = new Nestedsetbie([
        //     'table' => '{module}_catalogues',
        //     'foreignkey' => '{module}_catalogue_id',
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
        ${module}Catalogues = $this->{module}CatalogueRepository->pagination(
            $this->paginateselect(),
            $condition,
            $perPage,
            ['path' => '{module}/catalogue/index'],
            [
                '{module}_catalogues.lft',
                'ASC'
            ],
            [
                [
                    '{module}_catalogue_language as tb2',
                    'tb2.{module}_catalogue_id',
                    '=',
                    '{module}_catalogues.id'
                ]
            ],
        );
        return ${module}Catalogues;
    }
    public function create(Request $request, $languageId)
    {
        DB::beginTransaction();
        try {
            ${module}Catalogue = $this->createCatalogue($request);
            if (${module}Catalogue->id > 0) {
                $this->updateLanguageForCatalogue(${module}Catalogue, $request, $languageId);
                $this->createRouter(${module}Catalogue, $request, $this->controllerName, $languageId);
                $this->nestedset = new Nestedsetbie([
                    'table' => '{module}_catalogues',
                    'foreignkey' => '{module}_catalogue_id',
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
            ${module}Catalogue = $this->{module}CatalogueRepository->findById($id);
            $flag = $this->updateCatalogue(${module}Catalogue, $request);
            if ($flag) {
                $this->updateLanguageForCatalogue(${module}Catalogue, $request, $languageId);
                $this->updateRouter(${module}Catalogue, $request, $this->controllerName, $languageId);
                $this->nestedset = new Nestedsetbie([
                    'table' => '{module}_catalogues',
                    'foreignkey' => '{module}_catalogue_id',
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
            ${module}Catalogue = $this->{module}CatalogueRepository->forceDelete($id);
            $this->routerRepository->forceDeleteByCondition([
                ['module_id', '=', $id],
                ['controllers', '=', 'App\Http\Controller\Frontend\{class}CatalogueController']
            ]);
            $this->nestedset = new Nestedsetbie([
                'table' => '{module}_catalogues',
                'foreignkey' => '{module}_catalogue_id',
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
    public function updateStatus(${module} = [])
    {
        DB::beginTransaction();
        try {
            $payload[${module}['field']] = ((${module}['value'] == 1) ? 2 : 1);
            ${module}Catalogue = $this->{module}CatalogueRepository->update(${module}['modelId'], $payload);
            // $this->changeUserStatus(${module}, $payload[${module}['field']]);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }
    public function updateStatusAll(${module})
    {
        DB::beginTransaction();
        try {
            $payload[${module}['field']] = ${module}['value'];
            $flag = $this->{module}CatalogueRepository->updateByWhereIn('id', ${module}['id'], $payload);
            // $this->changeUserStatus(${module}, ${module}['value']);
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
        ${module}Catalogue = $this->{module}CatalogueRepository->create($payload);
        return ${module}Catalogue;
    }
    private function updateCatalogue(${module}Catalogue, $request)
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
        $flag = $this->{module}CatalogueRepository->update(${module}Catalogue->id, $payload);
        return $flag;
    }

    public function updateLanguageForCatalogue(${module}Catalogue, $request, $languageId)
    {
        $payload = $this->formatLanguagePayload(${module}Catalogue, $request, $languageId);
        ${module}Catalogue->languages()->detach([$languageId, ${module}Catalogue->id]);
        $language = $this->{module}CatalogueRepository->createPivot(${module}Catalogue, $payload, 'languages');
        return $language;
    }
    private function formatLanguagePayload(${module}Catalogue, $request, $languageId)
    {
        $payload = $request->only($this->payloadLanguage());
        $payload['canonical'] = Str::slug($payload['canonical']);
        $payload['language_id'] = $languageId;
        $payload['{module}_catalogue_id'] = ${module}Catalogue->id;
        return $payload;
    }
    private function paginateselect()
    {
        return ['{module}_catalogues.id', '{module}_catalogues.publish', '{module}_catalogues.image', '{module}_catalogues.level', '{module}_catalogues.order', 'tb2.name', 'tb2.canonical'];
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
