<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
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
            'canonical' => 'required|unique:routers',
            'post_catalogue_id' => 'gt:0',
        ];
    }
    public function messages(): array
    {
        return [
            'name.required' => 'Bạn chưa nhập vào ô tiêu đề!',
            'canonical.required' => 'Bạn chưa nhập vào ô đường dẫn!',
            'canonical.unique' => 'Đường dẫn đã tồn tại, Hãy chọn đường dẫn khác!',
            'post_catalogue_id.gt' => 'Bạn phải nhập vào danh mục cha',
        ];
    }
}
