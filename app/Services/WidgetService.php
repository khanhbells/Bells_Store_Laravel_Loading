<?php

namespace App\Services;

use App\Services\Interfaces\WidgetServiceInterface;
use App\Repositories\Interfaces\WidgetRepositoryInterface as WidgetRepository;
use App\Repositories\Interfaces\PromotionRepositoryInterface as PromotionRepository;
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
    public function __construct(
        WidgetRepository $widgetRepository,
        PromotionRepository $promotionRepository
    ) {
        $this->widgetRepository = $widgetRepository;
        $this->promotionRepository = $promotionRepository;
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
    public function findWidgetByKeyword(string $keyword = '', int $language = 1, $param = [])
    {
        $widget = $this->widgetRepository->findByCondition(
            [
                ['keyword', '=', $keyword],
                config('app.general.defaultPublish')
            ]
        );
        if (!is_null($widget)) {
            $class = loadClass($widget->model);
            $agrument = $this->widgetAgrument($widget, $language, $param);
            $object = $class->findByCondition(...$agrument);
            $model = lcfirst(str_replace('Catalogue', '', $widget->model));
            if (strpos($widget->model, 'Catalogue') && isset($param['children']) && $model == 'product') {
                if (count($object)) {
                    foreach ($object as $key => $val) {
                        if ($val->id != 1) continue;
                        $productId = $val->products->pluck('id')->toArray();
                        $promotions = $this->promotionRepository->findByProduct($productId);
                        dd($promotions);
                    }
                }
            }

            return $object;
        }
    }
    private function widgetAgrument($widget, $language, $param)
    {
        $relation = [
            'languages' => function ($query) use ($language) {
                $query->where('language_id', $language);
            }
        ];
        $withCount = [];
        if (strpos($widget->model, 'Catalogue') && isset($param['children'])) {
            $model = lcfirst(str_replace('Catalogue', '', $widget->model)) . 's';
            $relation[$model] = function ($query) use ($param, $language) {
                $query->whereHas('languages', function ($query) use ($language) {
                    $query->where('language_id', $language);
                });
                $query->take(($param['limit']) ?? 8);
                $query->orderBy('order', 'desc');
            };
            $withCount[] = $model;
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
}
// $query->with('promotions', function ($query) use ($limit) {
//     $query->select(
//         'promotions.id',
//         'promotions.discountValue',
//         'promotions.discountType',
//         'promotions.maxDiscountValue',
//         DB::raw(
//             "
//                 IF(promotions.maxDiscountValue != 0,
//                     LEAST(
//                         CASE
//                             WHEN discountType = 'cash' THEN discountValue
//                             WHEN discountType = 'percent' THEN ((SELECT price FROM products
//                             WHERE products.id = product_id)*discountValue/100)
//                             ELSE 0
//                             END,
//                             promotions.maxDiscountValue
//                         ),
//                         CASE
//                             WHEN discountType = 'cash' THEN discountValue
//                             WHEN discountType = 'percent' THEN ((SELECT price FROM products
//                             WHERE products.id = product_id)*discountValue/100)
//                             ELSE 0
//                             END
//                 )
//                 as discount
//             "
//         )
//     );
//     $query->where('publish', 2);
//     $query->whereDate('endDate', '>', now());
//     $query->orderBy('discount', 'desc');
//     $query->take($limit);
// });
