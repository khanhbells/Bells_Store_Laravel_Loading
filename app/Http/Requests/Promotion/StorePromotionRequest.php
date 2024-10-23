<?php

namespace App\Http\Requests\Promotion;

use App\Enums\PromotionEnum;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\Promotion\OrderAmountRangeRule;
use App\Rules\Promotion\ProductAndQuantityRule;

class StorePromotionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules = [
            'name' => 'required',
            'code' => 'required|unique:promotions',
            'startDate' => 'required|custom_date_format',
        ];
        $date = $this->only('startDate', 'endDate');
        if (!$this->input('neverEndDate')) {
            $rules['endDate'] = 'required|custom_date_format|custom_after:startDate';
        }
        $method = $this->input('method');
        switch ($method) {
            case PromotionEnum::ORDER_AMOUNT_RANGE:
                $rules['method'] = [new OrderAmountRangeRule($this->input('promotion_order_amount_range'))];
                break;
            case PromotionEnum::PRODUCT_AND_QUANTITY:
                $rules['method'] = [new ProductAndQuantityRule($this->only('product_and_quantity', 'object'))];
                break;
            default:
                $rules['method'] = 'required|not_in:none';
                break;
        }




        return $rules;
    }
    public function messages(): array
    {
        $message = [
            'name.required' => 'Bạn chưa nhập tên của khuyến mại!',
            'code.required' => 'Bạn chưa nhập mã khuyến mại của khuyến mại!',
            'code.unique' => 'Mã khuyến mại đã tồn tại hãy nhập mã khuyến mại khác!',
            'startDate.required' => 'Bạn chưa nhập vào ngày bắt đầu khuyến mại!',
            'startDate.custom_date_format' => 'Ngày bắt đầu khuyến mại không đúng định dạng!',
            'endDate.required' => 'Bạn chưa nhập vào ngày kết thúc khuyến mại!',
            'endDate.custom_date_format' => 'Ngày kết thúc khuyến mại không đúng định dạng'
        ];
        $method = $this->input('method');
        if ($method === 'none') {
            $message['method.not_in'] = 'Bạn chưa chọn hình thức khuyến mại';
        }
        if (!$this->input('neverEndDate')) {
            $message['endDate.required'] = 'Bạn chưa chọn ngày kết thúc của khuyến mại';
            $message['endDate.custom_after'] = 'Ngày kết thúc khuyến mại phải lớn hơn ngày bắt đầu khuyến mại';
        }
        return $message;
    }
}
