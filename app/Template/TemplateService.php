<?php

namespace App\Services;

use App\Services\Interfaces\{Module}ServiceInterface;
use App\Services\BaseService;
use App\Repositories\Interfaces\{Module}RepositoryInterface as {Module}Repository;
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
 * Class {Module}Service
 * @package App\Services
 */
class {Module}Service extends BaseService implements {Module}ServiceInterface
{
    protected ${module}Repository;
    protected $nestedset;
    protected $language;
    protected $routerRepository;
    protected $controllerName = '{Module}Controller';
    public function __construct({Module}Repository ${module}Repository, Nestedsetbie $nestedset, RouterRepository $routerRepository)
    {
        $this->{module}Repository = ${module}Repository;
        $this->routerRepository = $routerRepository;
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
        ${module}s = $this->{module}Repository->pagination(
            $this->paginateselect(),
            $condition,
            $perPage,
            ['path' => '{moduleView}.index'],
            [
                '{tableName}.lft',
                'ASC'
            ],
            [
                [
                    '{pivotTableName} as tb2',
                    'tb2.{foreignKey}',
                    '=',
                    '{tableName}.id'
                ]
            ],
        );
        return ${module}s;
    }
    public function create(Request $request, $languageId)
    {
        DB::beginTransaction();
        try {
            ${module} = $this->createCatalogue($request);
            if (${module}->id > 0) {
                $this->updateLanguageForCatalogue(${module}, $request, $languageId);
                $this->createRouter(${module}, $request, $this->controllerName, $languageId);
                $this->nestedset = new Nestedsetbie([
                    'table' => '{tableName}',
                    'foreignkey' => '{foreignKey}',
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
            ${module} = $this->{module}Repository->findById($id);
            $flag = $this->updateCatalogue(${module}, $request);
            if ($flag) {
                $this->updateLanguageForCatalogue(${module}, $request, $languageId);
                $this->updateRouter(${module}, $request, $this->controllerName, $languageId);
                $this->nestedset = new Nestedsetbie([
                    'table' => '{tableName}',
                    'foreignkey' => '{foreignKey}',
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
            ${module} = $this->{module}Repository->forceDelete($id);
            $this->nestedset = new Nestedsetbie([
                'table' => '{tableName}',
                'foreignkey' => '{foreignKey}',
                'language_id' => $languageId,
            ]);
            $this->nestedset->Get('level ASC, order ASC');
            $this->nestedset->Recursive(0, $this->nestedset->Set());
            $this->nestedset->Action();
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }
    public function updateStatus($post = [])
    {
        DB::beginTransaction();
        try {
            $payload[$post['field']] = (($post['value'] == 1) ? 2 : 1);
            ${module} = $this->{module}Repository->update($post['modelId'], $payload);
            // $this->changeUserStatus($post, $payload[$post['field']]);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }
    public function updateStatusAll($post)
    {
        DB::beginTransaction();
        try {
            $payload[$post['field']] = $post['value'];
            $flag = $this->{module}Repository->updateByWhereIn('id', $post['id'], $payload);
            // $this->changeUserStatus($post, $post['value']);
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
        ${module} = $this->{module}Repository->create($payload);
        return ${module};
    }
    private function updateCatalogue(${module}, $request)
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
        $flag = $this->{module}Repository->update(${module}->id, $payload);
        return $flag;
    }

    public function updateLanguageForCatalogue(${module}, $request, $languageId)
    {
        $payload = $this->formatLanguagePayload(${module}, $request, $languageId);
        ${module}->languages()->detach([$languageId, ${module}->id]);
        $language = $this->{module}Repository->createPivot(${module}, $payload, 'languages');
        return $language;
    }
    private function formatLanguagePayload(${module}, $request, $languageId)
    {
        $payload = $request->only($this->payloadLanguage());
        $payload['canonical'] = Str::slug($payload['canonical']);
        $payload['language_id'] = $languageId;
        $payload['{foreignKey}'] = ${module}->id;
        return $payload;
    }
    private function paginateselect()
    {
        return ['{tableName}.id', '{tableName}.publish', '{tableName}.image', '{tableName}.level', '{tableName}.order', 'tb2.name', 'tb2.canonical'];
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
