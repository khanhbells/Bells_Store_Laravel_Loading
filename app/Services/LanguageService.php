<?php

namespace App\Services;

use App\Services\Interfaces\LanguageServiceInterface;
use App\Repositories\Interfaces\LanguageRepositoryInterface as LanguageRepository;
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
 * Class LanguageService
 * @package App\Services
 */
class LanguageService extends BaseService implements LanguageServiceInterface
{
    protected $languageRepository;
    protected $routerRepository;
    public function __construct(LanguageRepository $languageRepository, RouterRepository $routerRepository)
    {
        $this->languageRepository = $languageRepository;
        $this->routerRepository = $routerRepository;
    }

    public function paginate($request)
    {
        $condition['keyword'] = addslashes($request->input('keyword'));
        $condition['publish'] = $request->input('publish', -1);
        $perPage = $request->integer('perpage');
        // dd($condition);
        $languages = $this->languageRepository->pagination(
            $this->paginateselect(),
            $condition,
            $perPage,
            ['path' => 'language/index'],
        );
        // dd($languages);
        return $languages;
    }
    private function paginateselect()
    {
        return ['id', 'name', 'canonical', 'publish', 'image'];
    }
    public function create(Request $request)
    {
        DB::beginTransaction();

        try {
            $payload = $request->except(['_token', 'send']);
            $payload['user_id'] = Auth::id();
            $language = $this->languageRepository->create($payload);
            // dd($payload);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }
    public function update($id, Request $request)
    {
        DB::beginTransaction();

        try {
            $payload = $request->except(['_token', 'send']);
            $language = $this->languageRepository->update($id, $payload);
            // dd($payload);
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
            $language = $this->languageRepository->forceDelete($id);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }
    public function saveTranslate($option, $request)
    {
        DB::beginTransaction();
        try {
            $payload = [
                'name' => $request->input('translate_name'),
                'description' => $request->input('translate_description'),
                'content' => $request->input('translate_content'),
                'meta_title' => $request->input('translate_meta_title'),
                'meta_keyword' => $request->input('translate_meta_keyword'),
                'meta_description' => $request->input('translate_meta_description'),
                'canonical' => $request->input('translate_canonical'),
                $this->convertModelToField($option['model']) => $option['id'],
                'language_id' => $option['languageId']
            ];
            $controllerName = $option['model'] . 'Controller';
            $repositoryNamespage = '\App\Repositories\\' . ucfirst($option['model']) . 'Repository';
            if (class_exists($repositoryNamespage)) {
                $repositoryInstance = app($repositoryNamespage);
            }
            $model = $repositoryInstance->findById($option['id']);
            $model->languages()->detach([$option['languageId'], $model->id]);
            $repositoryInstance->createPivot($model, $payload, 'languages');
            $this->routerRepository->forceDeleteByCondition(
                [
                    ['module_id', '=', $option['id']],
                    ['controllers', '=', 'App\Http\Controller\Frontend\\' . $controllerName],
                    ['language_id', '=', $option['languageId']]
                ]
            );
            $router = [
                'canonical' => Str::slug($request->input('translate_canonical')),
                'module_id' => $model->id,
                'language_id' => $option['languageId'],
                'controllers' => 'App\Http\Controller\Frontend\\' . $controllerName . '',
            ];
            $this->routerRepository->create($router);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }
    private function convertModelToField($model)
    {
        $temp = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $model));
        return $temp . '_id';
    }
    public function switch($id)
    {
        DB::beginTransaction();
        try {
            $language = $this->languageRepository->update($id, ['current' => 1]);
            $payload = ['current' => 0];
            $where = [
                ['id', '!=', $id]
            ];
            $this->languageRepository->updateByWhere($where, $payload);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }
}
