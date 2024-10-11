<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\CheckMenuItem;
use Illuminate\Validation\Rule;

class StoreMenuRequest extends FormRequest
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
            'menu_catalogue_id' => 'gt:0',
            'menu.name' => [
                'required',
            ]
        ];
    }
    public function messages(): array
    {
        return [
            'menu_catalogue_id.gt' => 'Bạn chưa chọn vị trí của menu!',
            'menu.name.required' => 'Bạn phải tạo ít nhất 1 menu'
        ];
    }
}
