<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TranslateRequest extends FormRequest
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
            'translate_name' => 'required',
            'translate_canonical' => 'required|unique:routers,canonical,' . $this->id . ',module_id',
        ];
    }
    public function messages(): array
    {
        return [
            'translate_name.required' => 'Bạn chưa nhập vào tên tiêu đề tiếng anh!',
            'translate_canonical.required' => 'Bạn chưa nhập vào từ khóa của ngôn ngữ!',
            'translate_canonical.unique' => 'Từ khóa đã tồn tại hãy chọn từ khóa khác!'
        ];
    }
}
