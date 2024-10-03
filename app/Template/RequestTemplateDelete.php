<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\{Module};
use App\Rules\Check{Module}ChildrenRule;

class Delete{Module}Request extends FormRequest
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
    public function rules(): array
    {
        $id = $this->route('id');
        return [
            'name' => [
                new Check{Module}ChildrenRule($id)
            ]
        ];
    }
}
