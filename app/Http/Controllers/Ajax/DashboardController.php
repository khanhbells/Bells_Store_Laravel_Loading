<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Language;
use Illuminate\Support\Str;


class DashboardController extends Controller
{
    protected $language;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $locale = app()->getLocale();
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            return $next($request);
        });
    }
    public function changeStatus(Request $request)
    {
        $post = $request->input();
        $serviceInterfaceNamespage = '\App\Services\\' . ucfirst($post['model']) . 'Service';
        if (class_exists($serviceInterfaceNamespage)) {
            $serviceInstance = app($serviceInterfaceNamespage);
        }
        $flag = $serviceInstance->updateStatus($post);
        return response()->json(['flag' => $flag]);
    }
    public function changeStatusAll(Request $request)
    {
        $post = $request->input();
        $serviceInterfaceNamespage = '\App\Services\\' . ucfirst($post['model']) . 'Service';
        if (class_exists($serviceInterfaceNamespage)) {
            $serviceInstance = app($serviceInterfaceNamespage);
        }
        $flag = $serviceInstance->updateStatusAll($post);
        return response()->json(['flag' => $flag]);
    }
    public function getMenu(Request $request)
    {
        $model = $request->input('model');
        $serviceInterfaceNamespage = '\App\Repositories\\' . ucfirst($model) . 'Repository';
        if (class_exists($serviceInterfaceNamespage)) {
            $serviceInstance = app($serviceInterfaceNamespage);
        }

        $agruments = $this->paginationAgrument($model);
        $object = $serviceInstance->pagination(...array_values($agruments));
        return response()->json(['data' => $object]);
    }
    private function paginationAgrument(string $model = ''): array
    {
        $model = Str::snake($model);
        $join = [
            [$model . '_language as tb2', 'tb2.' . $model . '_id', '=', $model . 's.id']
        ];
        if (strpos($model, '_catalogue') === false) {
            $join[] = ['' . $model . '_catalogue_' . $model . ' as tb3', '' . $model . 's.id', '=', 'tb3.' . $model . '_id'];
        }
        return [
            'select' => ['id', 'name'],
            'condition' => [
                'where' => [
                    ['tb2.language_id', '=', $this->language]
                ]
            ],
            'perpage' => 10,
            'paginationConfig' => [
                'path' => $model . '/index'
            ],
            'orderBy' => [
                $model . 's.id',
                'DESC'
            ],
            'join' => $join,
            'relations' => [],
        ];
    }
}
// array:2 [ // app\Http\Controllers\Ajax\LocationController.php:22
//     "data" => array:1 [
//       "province_id" => "01"
//     ]
//     "target" => "districts"
//   ]