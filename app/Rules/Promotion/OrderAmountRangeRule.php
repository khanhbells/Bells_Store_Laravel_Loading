<?php

namespace App\Rules\Promotion;

use Illuminate\Contracts\Validation\Rule;

class OrderAmountRangeRule implements Rule
{
    protected $data = [];
    protected $errorMessage = ''; // Thuộc tính để lưu trữ thông báo lỗi tùy chỉnh

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function passes($attribute, $value)
    {
        if (
            !isset($this->data['amountFrom'])
            || !isset($this->data['amountTo'])
            || !isset($this->data['amountValue'])
            || count($this->data['amountFrom']) == 0
            || $this->data['amountFrom'][0] == ''
        ) {
            $this->errorMessage = 'Bạn phải khởi tạo giá trị cho khoảng khuyến mại';
            return false;
        }
        if (in_array(0, $this->data['amountValue']) || in_array('', $this->data['amountValue'])) {
            $this->errorMessage = 'Giá trị khuyến mãi không được để trống hoặc bằng 0'; // Lỗi này khi có giá trị rỗng hoặc 0
            return false;
        }
        $conflict = false;
        //Quy hoạch động
        for ($i = 0; $i < count($this->data['amountFrom']); $i++) {
            $amountFrom_1 = convert_price($this->data['amountFrom'][$i]);
            $amountTo_1 = convert_price($this->data['amountTo'][$i]);
            if ($amountFrom_1 >= $amountTo_1) {
                $conflict = true;
                break;
            }
            for ($j = 0; $j < count($this->data['amountFrom']); $j++) {
                if ($i !== $j) {
                    $amountFrom_2 = convert_price($this->data['amountFrom'][$j]);
                    $amountTo_2 = convert_price($this->data['amountTo'][$j]);
                    if ($amountFrom_1 <= $amountTo_2 && $amountTo_1 >= $amountFrom_2) {
                        $conflict = true;
                        break 2;
                    }
                }
            }
        }
        if ($conflict) {
            $this->errorMessage = 'Có xung đột giữa các khoảng giá trị khuyến mại! Hãy kiểm tra lại'; // Lỗi này khi có giá trị rỗng hoặc 0
            return false;
        }
        // Nếu không có lỗi nào thì trả về true
        return true;
    }

    public function message()
    {
        // Trả về thông báo lỗi tùy chỉnh
        return $this->errorMessage ?: 'Có lỗi xảy ra trong quá trình kiểm tra giá trị'; // Thông báo mặc định
    }
}
