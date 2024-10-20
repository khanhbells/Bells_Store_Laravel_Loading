<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
{
    /**
     * Determine if the Customer is authorized to make this request.
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
            // kiem tra trung email khi update ngoai tru email cua chinh no thong qua id
            'email' => 'required|string|email|unique:customers,email,' . $this->id . '|max:191',
            'name' => 'required|string',
            'customer_catalogue_id' => 'gt:0', //gt bat buoc phai lon hon 0
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
        ];
    }
}
