<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    /**
     * Determine if the customer is authorized to make this request.
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
            'email' => 'required|string|email|unique:customers|max:191',
            'name' => 'required|string',
            'customer_catalogue_id' => 'gt:0', //gt bat buoc phai lon hon 0
            'password' => 'required|string|min:6',
            're_password' => 'required|string|same:password',
            'birthday' => 'required',
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
            'customer_catalogue_id.gt' => 'Bạn chưa chọn nhóm thành viên!',
            'password.required' => 'Bạn chưa nhập vào mật khẩu!',
            're_password.same' => 'Mật khẩu không khớp!',
            're_password.required' => 'Nhập lại mật khẩu không được để trống!',
            'birthday.required' => 'Ngày sinh không được để trống'
        ];
    }
}
