<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CheckMenuItem implements Rule
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function passes($attribute, $value)
    {
        if ($flag == false) {
            // Return false to indicate the validation failed
            return false;
        }

        return true;
    }

    public function message()
    {
        // Return the custom error message
        return 'Không thể xóa do vẫn còn danh mục con';
    }
}
