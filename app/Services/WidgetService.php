<?php

namespace App\Services;

use App\Services\Interfaces\WidgetServiceInterface;
use App\Repositories\Interfaces\WidgetRepositoryInterface as WidgetRepository;
use App\Repositories\Interfaces\PromotionRepositoryInterface as PromotionRepository;
use App\Repositories\Interfaces\ProductCatalogueRepositoryInterface as ProductCatalogueRepository;
use App\Services\Interfaces\ProductServiceInterface as ProductService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Exception;

/**
 * Class WidgetService
 * @package App\Services
 */
class WidgetService extends BaseService implements WidgetServiceInterface
{
    protected $widgetRepository;
    protected $promotionRepository;
    protected $productService;
    protected $productCatalogueRepository;
    public function __construct(
        WidgetRepository $widgetRepository,
        PromotionRepository $promotionRepository,
        ProductService $productService,
        ProductCatalogueRepository $productCatalogueRepository,
    ) {
        $this->widgetRepository = $widgetRepository;
        $this->promotionRepository = $promotionRepository;
        $this->productService = $productService;
        $this->productCatalogueRepository = $productCatalogueRepository;
    }

    public function paginate($request)
    {
        $condition['keyword'] = addslashes($request->input('keyword'));
        $condition['publish'] = $request->input('publish', -1);
        $perPage = $request->integer('perpage');
        // dd($condition);
        $widgets = $this->widgetRepository->pagination(
            $this->paginateselect(),
            $condition,
            $perPage,
            ['path' => 'widget/index'],
        );
        // dd($widgets);
        return $widgets;
    }
    public function create(Request $request, $languageId)
    {
        DB::beginTransaction();

        try {
            $payload = $request->only('name', 'keyword', 'short_code', 'description', 'album', 'model');
            $payload['model_id'] = $request->input('modelItem.id');
            $payload['album'] = $this->formatAlbum($payload['album']);
            $payload['description'] = [
                $languageId => $payload['description']
            ];
            $this->widgetRepository->create($payload);

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }
    public function update($id, Request $request, $languageId)
    {
        DB::beginTransaction();

        try {
            $payload = $request->only('name', 'keyword', 'short_code', 'description', 'album', 'model');
            $payload['model_id'] = $request->input('modelItem.id');
            $payload['album'] = $this->formatAlbum($payload['album']);
            $payload['description'] = [
                $languageId => $payload['description']
            ];
            $widget = $this->widgetRepository->update($id, $payload);
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
            $widget = $this->widgetRepository->forceDelete($id);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function saveTranslate($request)
    {
        DB::beginTransaction();
        try {
            $temp = [];
            $languageId = $request->input('translateId');
            $widget = $this->widgetRepository->findById($request->input('widgetId'));
            $temp = $widget->description;
            $temp[$languageId] = $request->input('translate_description');
            $payload['description'] = $temp;
            $this->widgetRepository->update($widget->id, $payload);
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
            $widget = $this->widgetRepository->update($post['modelId'], $payload);
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
            $flag = $this->widgetRepository->updateByWhereIn('id', $post['id'], $payload);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }

    private function convertBirthdayDate($birthday = '')
    {
        $carbonDate = Carbon::createFromFormat('Y-m-d', $birthday);
        $birthday = $carbonDate->format('Y-m-d H:i:s');
        return $birthday;
    }
    private function paginateselect()
    {
        return ['id', 'name', 'keyword', 'short_code', 'publish', 'description'];
    }


    // FRONTEND SERVICE



    public function getWidget(array $params = [], int $language)
    {
        $whereIn = [];
        $whereInField = 'keyword';
        if (count($params)) {
            foreach ($params as $key => $val) {
                $whereIn[] = $val['keyword'];
            }
        }
        $widgets = $this->widgetRepository->getWidgetWhereIn($whereIn);
        if (!is_null($widgets)) {
            $temp = [];
            foreach ($widgets as $key => $widget) {
                $class = loadClass($widget->model);
                $agrument = $this->widgetAgrument($widget, $language, $params[$key]);
                $object = $class->findByCondition(...$agrument);
                $model = lcfirst(str_replace('Catalogue', '', $widget->model));
                if (count($object) && strpos($widget->model, 'Catalogue')) {
                    $objectId = $object->pluck('id')->toArray(); //DS ID cua danh muc cha
                    if (isset($params[$key]['children']) && $params[$key]['children']) {
                        //Đang lấy ra danh mục cấp 1
                        $childrenAgrument = $this->childrenAgrument($objectId, $language);
                        $object->childrens  = $class->findByCondition(...$childrenAgrument);

                        // -------------------------------------------------------------

                    }
                    //-------------LAY SAN PHAM --------------------------

                    // if (isset($params[$key]['object']) &&  $params[$key]['object'] === true) {
                    // }
                    $parameters = implode(',', $objectId);
                    $childId = $class->recursiveCategory($parameters, $model);
                    $ids = [];
                    foreach ($childId as $child_id) {
                        $ids[] = $child_id->id;
                    }
                    $classRepo = loadClass(ucfirst($model));
                    $replace = $model . 's';
                    foreach ($object as $val) {
                        if ($val->rgt - $val->lft > 1) {
                            $val->{$replace} = $classRepo->findObjectByCategoryIds($ids, $model);
                        }
                        if (isset($params[$key]['promotion']) && $params[$key]['promotion'] == true) {
                            $productId = $val->{$replace}->pluck('id')->toArray();
                            $val->{$replace} = $this->productService->combineProductAndPromotion($productId, $val->{$replace});
                        }
                    }
                    $widget->object = $object;
                }
                $temp[$widget->keyword] = $widgets[$key];
            }
        }
        return $temp;
    }

    private function widgetAgrument($widget, $language, $param)
    {
        $relation = [
            'languages' => function ($query) use ($language) {
                $query->where('language_id', $language);
            }
        ];
        $withCount = [];

        if (strpos($widget->model, 'Catalogue') && isset($param['promotion'])) {

            $model = lcfirst(str_replace('Catalogue', '', $widget->model)) . 's';
            $relation[$model] = function ($query) use ($param, $language) {
                $query->whereHas('languages', function ($query) use ($language) {
                    $query->where('language_id', $language);
                });
                $query->take(($param['limit']) ?? 8);
                $query->orderBy('order', 'desc');
            };

            if (isset($param['countObject'])) {
                $withCount[] = $model;
            }
        }
        return [
            'condition' => [
                config('app.general.defaultPublish')
            ],
            'flag' => true,
            'relation' => $relation,
            'param' => [
                'whereIn' => $widget->model_id,
                'whereInField' => 'id'
            ],
            'withCount' => $withCount
        ];
    }

    private function childrenAgrument($objectId, $language)
    {
        return [
            'condition' => [
                config('app.general.defaultPublish')
            ],
            'flag' => true,
            'relation' => [
                'languages' => function ($query) use ($language) {
                    $query->where('language_id', $language);
                }
            ],
            'param' => [
                'whereIn' => $objectId,
                'whereInField' => 'parent_id'
            ]
        ];
    }
}







// public function findWidgetByKeyword(string $keyword = '', int $language = 1, $param = [])
// {
//     $widget = $this->widgetRepository->findByCondition(
//         [
//             ['keyword', '=', $keyword],
//             config('app.general.defaultPublish')
//         ]
//     );
//     if (!is_null($widget)) {
//         $class = loadClass($widget->model);
//         $agrument = $this->widgetAgrument($widget, $language, $param);
//         $object = $class->findByCondition(...$agrument);
//         $model = lcfirst(str_replace('Catalogue', '', $widget->model));

//         if (count($object)) {
//             foreach ($object as $key_1 => $val) {
//                 if ($model === 'product' && isset($param['object']) && $param['object'] == true) {
//                     $productId = $val->products->pluck('id')->toArray();
//                     $val->products = $this->productService->combineProductAndPromotion($productId, $val->products);
//                 }

//                 if (isset($param['children']) && $param['children'] == true) {
//                     $val->childrens = $this->productCatalogueRepository->findByCondition(
//                         [
//                             ['lft', '>', $val->lft],
//                             ['rgt', '<', $val->rgt],
//                             config('app.general.defaultPublish'),
//                         ],
//                         true
//                     );
//                 }
//             }
//         }
//         return $object;
//     }
// }
