<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWidgetRequest extends FormRequest
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
        return [
            'name' => 'required',
            'keyword' => 'required|unique:widgets,keyword,' . $this->id . '',
            'short_code' => 'required|unique:widgets,short_code,' . $this->id . '',
        ];
    }
    public function messages(): array
    {
        return [
            'name.required' => 'Bạn chưa nhập tên của widget!',
            'keyword.required' => 'Bạn chưa nhập từ khóa của widget!',
            'keyword.unique' => 'Từ khóa đã tồn tại hãy nhập từ khóa khác!',
            'short_code.required' => 'Bạn chưa nhập vào shortcode của widget!',
            'short_code.unique' => 'ShortCode đã tồn tại hãy nhập ShortCode khác!',
        ];
    }
}
