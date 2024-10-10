<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMenuCatalogueRequest extends FormRequest
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
            'keyword' => 'required|unique:menu_catalogues',
        ];
    }
    public function messages(): array
    {
        return [
            'name.required' => 'Bạn chưa nhập vào tên menu!',
            'keyword.required' => 'Bạn chưa nhập vào từ khóa của menu!',
            'keyword.unique' => 'Nhóm menu đã tồn tại!',
        ];
    }
}
