<?php

namespace App\Rules\Promotion;

use Illuminate\Contracts\Validation\Rule;

class ProductAndQuantityRule implements Rule
{
    protected $data = [];
    protected $errorMessage = ''; // Thuộc tính để lưu trữ thông báo lỗi tùy chỉnh

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function passes($attribute, $value)
    {
        if ($this->data['product_and_quantity']['quantity'] == 0) {
            $this->errorMessage = 'Bạn phải nhập số lượng tối thiểu để hưởng chiết khấu';
            return false;
        }
        if ($this->data['product_and_quantity']['discountValue'] == 0) {
            $this->errorMessage = 'Bạn phải nhập vào giá trị của chiết khấu';
            return false;
        }
        if (!isset($this->data['object'])) {
            $this->errorMessage = 'Bạn chưa chọn đối tượng áp dụng chiết khấu';
            return false;
        }
        return true;
    }

    public function message()
    {
        // Trả về thông báo lỗi tùy chỉnh
        return $this->errorMessage ?: 'Có lỗi xảy ra trong quá trình kiểm tra giá trị'; // Thông báo mặc định
    }
}
