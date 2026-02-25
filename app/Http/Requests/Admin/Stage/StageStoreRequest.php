<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Stage;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StageStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:1', 'max:150', Rule::unique('stages', 'name')],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Введите название направления',
            'name.unique' => 'Направление с таким названием уже существует',
            'name.max' => 'Название не должно превышать 150 символов',
        ];
    }
}
