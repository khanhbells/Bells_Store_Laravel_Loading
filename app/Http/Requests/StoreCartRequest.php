<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCartRequest extends FormRequest
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
            'fullname' => 'required',
            'phone' => 'required',
            'email' => 'required|email',
            'address' => 'required',
        ];
    }
    public function messages(): array
    {
        return [
            'fullname.required' => 'Bạn chưa nhập vào họ tên!',
            'phone.required' => 'Bạn chưa nhập vào số điện thoại!',
            'address.required' => 'Bạn chưa nhập vào địa chỉ nơi nhận!',
            'email.required' => 'Bạn chưa nhập vào email!',
            'email.email' => 'Email không đúng định dạng!',
        ];
    }
}
