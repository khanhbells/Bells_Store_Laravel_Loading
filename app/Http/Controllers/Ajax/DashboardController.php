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
        $page = ($request->input('page')) ?? 1;
        $keyword = ($request->string('keyword')) ?? null;
        $serviceInterfaceNamespage = '\App\Repositories\\' . ucfirst($model) . 'Repository';
        if (class_exists($serviceInterfaceNamespage)) {
            $serviceInstance = app($serviceInterfaceNamespage);
        }

        $agruments = $this->paginationAgrument($model, $keyword);
        $object = $serviceInstance->pagination(...array_values($agruments));
        return response()->json($object);
    }
    private function paginationAgrument(string $model = '', string $keyword): array
    {
        $model = Str::snake($model);
        $join = [
            [$model . '_language as tb2', 'tb2.' . $model . '_id', '=', $model . 's.id']
        ];
        if (strpos($model, '_catalogue') === false) {
            $join[] = ['' . $model . '_catalogue_' . $model . ' as tb3', '' . $model . 's.id', '=', 'tb3.' . $model . '_id'];
        }
        $condition = [
            'where' => [
                ['tb2.language_id', '=', $this->language]
            ]
        ];
        if (!is_null($keyword)) {
            $condition['keyword'] = addslashes($keyword);
        }
        return [
            'select' => ['id', 'name', 'canonical'],
            'condition' => $condition,
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
    public function findModelObject(Request $request)
    {
        $get = $request->input();
        $alias = Str::snake($get['model']) . '_language';
        $class = loadClass($get['model']);
        $object = $class->findWidgetItem(
            [
                [
                    'name',
                    'LIKE',
                    '%' . $get['keyword'] . '%'
                ]
            ],
            $this->language,
            $alias
        );
        return response()->json($object);
    }
    public function findPromotionObject(Request $request)
    {
        $get = $request->input();
        $model = $get['option']['model'];
        $alias = Str::snake($model) . '_language';
        $keyword = $get['search'];
        $class = loadClass(($model));
        $object = $class->findWidgetItem(
            [
                [
                    'name',
                    'LIKE',
                    '%' . $keyword . '%'
                ]
            ],
            $this->language,
            $alias
        );
        $temp = [];
        if (count($object)) {
            foreach ($object as $key => $val) {
                $temp[] = [
                    'id' => $val->id,
                    'text' => $val->languages->first()->pivot->name,
                ];
            }
        }
        return response()->json(array('items' => $temp));
    }
}
// array:2 [ // app\Http\Controllers\Ajax\LocationController.php:22
//     "data" => array:1 [
//       "province_id" => "01"
//     ]
//     "target" => "districts"
//   ]