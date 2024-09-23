<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'email' => 'required|string|email|unique:users|max:191',
            'name' => 'required|string',
            'user_catalogue_id' => 'gt:0', //gt bat buoc phai lon hon 0
            'password' => 'required|string|min:6',
            're_password' => 'required|string|same:password',
        ];
    }
    public function messages(): array
    {
        return [
            'email.required' => 'Bạn chưa nhập vào email!',
            'email.email' => 'Email không đúng định dạng!',
            'email.unique' => 'Email đã tồn tại!',
            'email.string' => 'Email phải là dạng ký tự!',
            'email.max' => 'Độ dài email tối đa 191 ký tự!',
            'name.required' => 'Bạn chưa nhập họ tên!',
            'name.string' => 'Họ tên phải là dạng ký tự!',
            'user_catalogue_id.gt' => 'Bạn chưa chọn nhóm thành viên!',
            'password.required' => 'Bạn chưa nhập vào mật khẩu!',
            're_password.same' => 'Mật khẩu không khớp!',
            're_password.required' => 'Nhập lại mật khẩu không được để trống!',
        ];
    }
}
