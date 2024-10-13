<?php

namespace App\Services;

use App\Services\Interfaces\BaseServiceInterface as BaseServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Classes\Nestedsetbie;
use Illuminate\Support\Str;
use App\Repositories\Interfaces\RouterRepositoryInterface as RouterRepository;

/**
 * Class LanguageService
 * @package App\Services
 */
class BaseService implements BaseServiceInterface
{
    protected $nestedset;
    protected $language;
    protected $routerRepository;
    protected $controllerName;
    public function __construct(BaseServiceInterface $baseServiceInterface, Nestedsetbie $nestedset, RouterRepository $routerRepository)
    {
        $this->language = $this->currentLanguage();
        $this->nestedset = new Nestedsetbie([
            'table' => 'post_catalogues',
            'foreignkey' => 'post_catalogue_id',
            'language_id' => $this->language,
        ]);
        $this->routerRepository = $routerRepository;
    }
    public function currentLanguage()
    {
        return 1;
    }
    public function formatAlbum($albumArray)
    {
        foreach ($albumArray as &$image) {
            if (strpos($image, 'http://localhost:81/laravelversion1.com/public') !== false) {
                $image = str_replace('http://localhost:81/laravelversion1.com/public', '', $image);
            } elseif (strpos($image, '/laravelversion1.com/public') !== false) {
                $image = str_replace('/laravelversion1.com/public', '', $image);
            }
        }
        return (!empty($albumArray)) ? json_encode($albumArray) : ''; // Mã hóa lại thành chuỗi JSON
    }
    public function formatImage($image)
    {
        if (strpos($image, 'http://localhost:81/laravelversion1.com/public') !== false) {
            return str_replace('http://localhost:81/laravelversion1.com/public', '', $image);
        } elseif (strpos($image, '/laravelversion1.com/public') !== false) {
            return str_replace('/laravelversion1.com/public', '', $image);
        }
        return $image;
    }
    public function nestedset()
    {
        $this->nestedset->Get('level ASC, order ASC');
        $this->nestedset->Recursive(0, $this->nestedset->Set());
        $this->nestedset->Action();
    }
    public function createRouter($model, $request, $controllerName, $languageId)
    {
        $router = $this->formatRouterPayload($model, $request, $controllerName, $languageId);
        $this->routerRepository->create($router);
    }
    public function updateRouter($model, $request, $controllerName, $languageId)
    {
        // Định dạng payload cho router
        $payload = $this->formatRouterPayload($model, $request, $controllerName, $languageId);

        // Điều kiện để tìm router trong database
        $condition = [
            ['module_Id', '=', $model->id],
            ['controllers', '=', 'App\Http\Controller\Frontend\\' . $controllerName . ''],
        ];

        // Tìm router theo điều kiện
        $router = $this->routerRepository->findByCondition($condition);
        // Nếu router tồn tại, thực hiện cập nhật
        if ($router) {
            $res = $this->routerRepository->update($router->id, $payload);
        }
        // Nếu router không tồn tại, thực hiện thêm mới
        else {
            $payload['module_Id'] = $model->id; // Gán giá trị module_Id vào payload nếu cần thiết
            $payload['controllers'] = 'App\Http\Controller\Frontend\\' . $controllerName; // Gán controllers vào payload
            $res = $this->routerRepository->create($payload); // Thêm bản ghi mới
        }
        return $res;
    }
    public function formatRouterPayload($model, $request, $controllerName, $languageId)
    {
        $router = [
            'canonical' => Str::slug($request->input('canonical')),
            'module_id' => $model->id,
            'language_id' => $languageId,
            'controllers' => 'App\Http\Controller\Frontend\\' . $controllerName . '',
        ];
        return $router;
    }
    public function formatJson($request, $inputName)
    {
        return ($request->input($inputName) && !empty($request->input($inputName))) ? json_encode($request->input($inputName)) : '';
    }
    public function updateStatus($post = [])
    {
        DB::beginTransaction();
        try {
            $model = lcfirst($post['model']) . 'Repository';
            $payload[$post['field']] = (($post['value'] == 1) ? 2 : 1);
            $post = $this->{$model}->update($post['modelId'], $payload);
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
            $model = lcfirst($post['model']) . 'Repository';
            $payload[$post['field']] = $post['value'];
            $flag = $this->{$model}->updateByWhereIn('id', $post['id'], $payload);
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
}
