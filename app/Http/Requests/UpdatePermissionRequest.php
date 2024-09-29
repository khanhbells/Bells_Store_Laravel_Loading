<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePermissionRequest extends FormRequest
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
            'canonical' => 'required|unique:permissions,canonical,' . $this->id . '',
        ];
    }
    public function messages(): array
    {
        return [
            'name.required' => 'Bạn chưa nhập vào tên ngôn ngữ!',
            'canonical.required' => 'Bạn chưa nhập vào từ khóa của ngôn ngữ!',
            'canonical.unique' => 'Từ khóa đã tồn tại hãy chọn từ khóa khác!'
        ];
    }
}