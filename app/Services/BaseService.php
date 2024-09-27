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
    public function formatRouterPayload($model, $request, $controllerName)
    {
        $router = [
            'canonical' => $request->input('canonical'),
            'module_id' => $model->id,
            'controllers' => 'App\Http\Controller\Frontend\\' . $controllerName . '',

        ];
        return $router;
    }
    public function createRouter($model, $request, $controllerName)
    {
        $router = $this->formatRouterPayload($model, $request, $controllerName);
        // dd($router);
        $this->routerRepository->create($router);
    }
    public function updateRouter($model, $request, $controllerName)
    {
        $payload = $this->formatRouterPayload($model, $request, $controllerName);
        $condition = [
            ['module_Id', '=', $model->id],
            ['controllers', '=', 'App\Http\Controller\Frontend\\' . $controllerName . ''],
        ];
        $router = $this->routerRepository->findByCondition($condition);
        $res = $this->routerRepository->update($router->id, $payload);
        return $res;
    }
}
