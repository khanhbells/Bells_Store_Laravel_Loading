<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

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
            'translate_canonical' => [
                'required',
                function ($attribute, $value, $fail) {
                    $option = $this->input('option');
                    $exist = DB::table('routers')->where('canonical', $value)->where(function ($query) use ($option) {
                        $query->where('module_id', '<>', $option['id'])->orWhere('language_id', '<>', $option['languageId']);
                    })->exists();
                    if ($exist) {
                        $fail('Đường dẫn đã tồn tại. Hãy chọn đường dẫn khác');
                    }
                }
            ],
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
