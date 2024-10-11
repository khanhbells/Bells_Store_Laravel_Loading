<?php

namespace App\Services;

use App\Services\Interfaces\{class}ServiceInterface;
use App\Services\BaseService;
use App\Repositories\Interfaces\{class}RepositoryInterface as {class}Repository;
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
 * Class {class}Service
 * @package App\Services
 */
class {class}Service extends BaseService implements {class}ServiceInterface
{
    protected ${module}Repository;
    protected $routerRepository;
    public function __construct({class}Repository ${module}Repository, RouterRepository $routerRepository)
    {
        $this->{module}Repository = ${module}Repository;
        $this->routerRepository = $routerRepository;
        $this->controllerName = '{class}Controller';
    }
    public function paginate($request, $languageId)
    {
        $perPage = $request->integer('perpage');
        $condition = [
            'keyword' => addslashes($request->input('keyword')),
            'publish' => $request->input('publish', -1),
            '{module}_catalogue_id' => $request->input('{module}_catalogue_id'),
            'where' => [
                ['tb2.language_id', '=', $languageId]
            ]
        ];
        $paginationConfig = [
            'path' => '{module}/index'
        ];
        $orderBy = ['{module}s.id', 'DESC'];
        $relations = ['{module}_catalogues'];
        $rawQuery = $this->whereRaw($request, $languageId);
        $joins = [
            ['{module}_language as tb2', 'tb2.{module}_id', '=', '{module}s.id'],
            ['{module}_catalogue_{module} as tb3', '{module}s.id', '=', 'tb3.{module}_id']
        ];
        ${module}s = $this->{module}Repository->pagination(
            $this->paginateselect(),
            $condition,
            $perPage,
            $paginationConfig,
            $orderBy,
            $joins,
            $relations,
            $rawQuery
        );
        return ${module}s;
    }
    public function create(Request $request, $languageId)
    {
        DB::beginTransaction();

        try {
            // dd($request);
            ${module} = $this->create{class}($request);
            if (${module}->id > 0) {
                $this->uploadLanguageFor{class}(${module}, $request, $languageId);
                $this->updateCatalogueFor{module}(${module}, $request);
                $this->createRouter(${module}, $request, $this->controllerName, $languageId);
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
            ${module} = $this->{module}Repository->findById($id);
            // dd(${module});
            if ($this->upload{class}(${module}, $request)) {
                $this->uploadLanguageFor{class}(${module}, $request, $languageId);
                $this->updateCatalogueFor{class}(${module}, $request);
                $this->updateRouter(${module}, $request, $this->controllerName, $languageId);
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
            ${module} = $this->{module}Repository->forceDelete($id);
            $this->routerRepository->forceDeleteByCondition([
                ['module_id', '=', $id],
                ['controllers', '=', 'App\Http\Controller\Frontend\{class}CatalogueController']
            ]);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return false;
        }
    }
    public function updateStatus(${module} = [])
    {
        DB::beginTransaction();
        try {
            $payload[${module}['field']] = ((${module}['value'] == 1) ? 2 : 1);
            ${module} = $this->{module}Repository->update(${module}['modelId'], $payload);
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
            $flag = $this->{module}Repository->updateByWhereIn('id', ${module}['id'], $payload);
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
    private function create{class}($request)
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
        ${module} = $this->{module}Repository->create($payload);
        return ${module};
    }



    private function upload{class}(${module}, $request)
    {
        $payload = $request->only($this->payload());
        if (isset($payload['album'])) {
            $payload['album'] = $this->formatAlbum($payload['album']);
        }
        if (isset($payload['image'])) {
            $payload['image'] = $this->formatImage($payload['image']);
        }
        // dd($payload);
        // Kiểm tra và xử lý cả hai trường hợp ảnh có tiền tố 'http://localhost:81/laravelversion1.com/public' hoặc '/laravelversion1.com/public'

        return $this->{module}Repository->update(${module}->id, $payload);
    }
    private function uploadLanguageFor{class}(${module}, $request, $languageId)
    {
        $payload = $request->only($this->payloadLanguage());
        $payload = $this->formatLanguagePayload($payload, ${module}->id, $languageId);
        ${module}->languages()->detach([$languageId, ${module}->id]);
        return $this->{module}Repository->createPivot(${module}, $payload, 'languages');
    }
    private function formatLanguagePayload($payload, ${module}Id, $languageId)
    {
        $payload['canonical'] = Str::slug($payload['canonical']);
        $payload['language_id'] = $languageId;
        $payload['{module}_id'] = ${module}Id;
        return $payload;
    }
    private function updateCatalogueFor{module}(${module}, $request)
    {
        ${module}->{module}_catalogues()->sync($this->catalogue($request));
    }
    private function catalogue($request)
    {
        if ($request->input('catalogue') != null) {
            return array_unique(array_merge($request->input('catalogue'), [$request->{module}_catalogue_id]));
        } else {
            return [$request->{module}_catalogue_id];
        }
    }
    private function whereRaw($request, $languageId)
    {
        $rawCondition = [];
        if ($request->integer('{module}_catalogue_id') > 0) {
            $rawCondition['whereRaw'] =  [
                [
                    'tb3.{module}_catalogue_id IN (
                        SELECT id
                        FROM {module}_catalogues
                        WHERE lft >= (SELECT lft FROM {module}_catalogues as pc WHERE pc.id = ?)
                        AND rgt <= (SELECT rgt FROM {module}_catalogues as pc WHERE pc.id =?)
                    )',
                    [$request->integer('{module}_catalogue_id'), $request->integer('{module}_catalogue_id')]
                ]
            ];
        }
        return $rawCondition;
    }
    private function paginateselect()
    {
        return ['{module}s.id', '{module}s.publish', '{module}s.image', '{module}s.order', 'tb2.name', 'tb2.canonical'];
    }
    private function payload()
    {
        return ['follow', 'publish', 'image', 'album', '{module}_catalogue_id'];
    }
    private function payloadLanguage()
    {
        return  ['name', 'description', 'content', 'meta_title', 'meta_keyword', 'meta_description', 'canonical'];
    }
}
