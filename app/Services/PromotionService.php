<?php

namespace App\Services;

use App\Enums\PromotionEnum;
use App\Services\Interfaces\PromotionServiceInterface;
use App\Repositories\Interfaces\PromotionRepositoryInterface as PromotionRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Exception;

/**
 * Class PromotionService
 * @package App\Services
 */
class PromotionService extends BaseService implements PromotionServiceInterface
{
    protected $promotionRepository;
    public function __construct(PromotionRepository $promotionRepository)
    {
        $this->promotionRepository = $promotionRepository;
    }

    public function paginate($request)
    {
        $condition['keyword'] = addslashes($request->input('keyword'));
        $condition['publish'] = $request->input('publish', -1);
        $perPage = $request->integer('perpage');
        // dd($condition);
        $promotions = $this->promotionRepository->pagination(
            $this->paginateselect(),
            $condition,
            $perPage,
            ['path' => 'promotion/index'],
        );
        return $promotions;
    }

    private function request($request)
    {
        $payload = $request->only(
            'name',
            'code',
            'description',
            'method',
            'startDate',
            'endDate',
            'neverEndDate',
        );
        // Chuyển đổi định dạng ngày tháng trước khi lưu
        $payload['startDate'] = Carbon::createFromFormat('d/m/Y H:i', $request->input('startDate'));
        if (isset($payload['endDate'])) {
            $payload['endDate'] = Carbon::createFromFormat('d/m/Y H:i', $request->input('endDate'));
        }
        if (isset($payload['neverEndDate'])) {
            $payload['endDate'] = null;
        } else {
            $payload['neverEndDate'] = null;
        }

        $payload['code'] = (empty($payload['code'])) ? time() : $payload['code'];
        switch ($payload['method']) {
            case PromotionEnum::ORDER_AMOUNT_RANGE:
                $payload[PromotionEnum::DISCOUNT] = $this->orderByRange($request);
                break;
            case PromotionEnum::PRODUCT_AND_QUANTITY:
                $payload[PromotionEnum::DISCOUNT] = $this->productAndQuantity($request);
                break;
        }

        return $payload;
    }

    public function create(Request $request, $languageId)
    {
        DB::beginTransaction();

        try {
            $payload = $this->request($request);
            $promotion = $this->promotionRepository->create($payload);
            if ($promotion->id > 0) {
                $this->handleRelation($request, $promotion);
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

    public function update($id, Request $request, $languageId)
    {
        DB::beginTransaction();

        try {
            $payload = $this->request($request);
            $promotion = $this->promotionRepository->update($id, $payload);
            if ($promotion->id > 0) {
                $this->handleRelation($request, $promotion, 'update');
            }
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

    private function handleRelation($request, $promotion, $method = 'create')
    {
        if ($request->input('method') === PromotionEnum::PRODUCT_AND_QUANTITY) {
            $object = $request->input('object');
            $payloadRelation = [];
            if (is_array($object) && count($object)) {
                foreach ($object['id'] as $key => $val) {
                    $productVariantUuId = $object['variant_uuid'][$key];
                    if ($productVariantUuId == 'null') {
                        $productVariantUuId = 0;
                    }

                    $payloadRelation[] = [
                        'product_id' => $val,
                        'variant_uuid' => $productVariantUuId,
                        'model' => $request->input(PromotionEnum::MODULE_TYPE)
                    ];
                }
            }

            if ($method == 'update') {
                $promotion->products()->detach();
            }
            $promotion->products()->sync($payloadRelation);
        }
    }

    private function handleSourceAndCondition($request)
    {
        $data = [
            'source' => [
                'status' => $request->input('source'),
                'data' => $request->input('sourceValue'),
            ],
            'apply' => [
                'status' => $request->input('applyStatus'),
                'data' => $request->input('applyValue'),
            ]
        ];
        if (!is_null($data['apply']['data'])) {
            foreach ($data['apply']['data'] as $key => $val) {
                $data['apply']['condition'][$val] = $request->input($val);
            }
        }
        return $data;
    }

    private function orderByRange($request)
    {
        $data['info'] = $request->input('promotion_order_amount_range');
        return $data + $this->handleSourceAndCondition($request);
    }

    private function productAndQuantity($request)
    {
        $data['info'] = $request->input('product_and_quantity');
        $data['info']['model'] = $request->input(PromotionEnum::MODULE_TYPE);
        $data['info']['object'] = $request->input('object');
        return $data + $this->handleSourceAndCondition($request);
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $promotion = $this->promotionRepository->forceDelete($id);
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
            $promotion = $this->promotionRepository->findById($request->input('promotionId'));
            $temp = $promotion->description;
            $temp[$languageId] = $request->input('translate_description');
            $payload['description'] = $temp;
            $this->promotionRepository->update($promotion->id, $payload);
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
            $promotion = $this->promotionRepository->update($post['modelId'], $payload);
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
            $flag = $this->promotionRepository->updateByWhereIn('id', $post['id'], $payload);
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
        return [
            'id',
            'name',
            'code',
            'discountInformation',
            'method',
            'neverEndDate',
            'startDate',
            'endDate',
            'publish',
            'order',
        ];
    }
}
