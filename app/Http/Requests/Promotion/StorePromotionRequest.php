<?php

namespace App\Http\Requests\Promotion;

use Illuminate\Foundation\Http\FormRequest;

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
            // 'code' => 'required|unique:promotions',
            'startDate' => 'required|custom_date_format',
        ];

        if (!$this->input('neverEndDate')) {
            $rules['endDate'] = 'required|custom_date_format|custom_after:startDate';
        }



        return $rules;
    }
    public function messages(): array
    {
        $message = [
            'name.required' => 'Bạn chưa nhập tên của khuyến mại!',
            'code.required' => 'Bạn chưa nhập mã khuyến mại của khuyến mại!',
            // 'code.unique' => 'Mã khuyến mại đã tồn tại hãy nhập từ khóa khác!',
            'startDate.required' => 'Bạn chưa nhập vào ngày bắt đầu khuyến mại!',
            'startDate.custom_date_format' => 'Ngày bắt đầu khuyến mại không đúng định dạng!',
            'endDate.required' => 'Bạn chưa nhập vào ngày kết thúc khuyến mại!',
            'endDate.custom_date_format' => 'Ngày kết thúc khuyến mại không đúng định dạng'
        ];

        if (!$this->input('neverEndDate')) {
            $message['endDate.required'] = 'Bạn chưa chọn ngày kết thúc của khuyến mại';
            $message['endDate.custom_after'] = 'Ngày kết thúc khuyến mại phải lớn hơn ngày bắt đầu khuyến mại';
        }
        return $message;
    }
}
