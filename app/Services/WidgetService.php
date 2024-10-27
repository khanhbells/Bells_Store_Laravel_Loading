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
            $payload['album'] = $this->formatAlbum($payload['album'], true);
            // dd($payload['album']);
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
                $replace = $model . 's';
                $service = $model . 'Service';
                if (count($object) && strpos($widget->model, 'Catalogue')) {
                    $classRepo = loadClass(ucfirst($model));
                    foreach ($object as $objectKey => $objectValue) {
                        if (isset($params[$key]['children']) && $params[$key]['children']) {
                            $childrenAgrument = $this->childrenAgrument([$objectValue->id], $language);
                            $objectValue->childrens  = $class->findByCondition(...$childrenAgrument);
                        }
                        //-------------LAY SAN PHAM --------------------------
                        // $parameters[] = $objectValue->id;
                        $childId = $class->recursiveCategory($objectValue->id, $model);
                        $ids = [];
                        foreach ($childId as $child_id) {
                            $ids[] = $child_id->id;
                        }
                        if ($objectValue->rgt - $objectValue->lft > 1) {
                            $objectValue->{$replace} = $classRepo->findObjectByCategoryIds($ids, $model, $language);
                        }
                        if (isset($params[$key]['promotion']) && $params[$key]['promotion'] == true) {
                            $productId = $objectValue->{$replace}->pluck('id')->toArray();
                            $objectValue->{$replace} = $this->{$service}->combineProductAndPromotion($productId, $objectValue->{$replace});
                        }
                        $widgets[$key]->object = $object;
                    }
                } else {
                    $productId = $object->pluck('id')->toArray();
                    $object = $this->{$service}->combineProductAndPromotion($productId, $object);
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

        if (strpos($widget->model, 'Catalogue')) {
            $model = lcfirst(str_replace('Catalogue', '', $widget->model)) . 's';
            if (isset($param['object'])) {
                $relation[$model] = function ($query) use ($param, $language) {
                    $query->whereHas('languages', function ($query) use ($language) {
                        $query->where('language_id', $language);
                    });
                    $query->take(($param['limit']) ?? 8);
                    $query->orderBy('order', 'desc');
                };
            }
            if (isset($param['countObject'])) {
                $withCount[] = $model;
            }
        } else {
            $model = lcfirst($widget->model . '_catalogues');
            $relation[$model] = function ($query) use ($language) {
                $query->with('languages', function ($query) use ($language) {
                    $query->where('language_id', $language);
                });
            };
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
