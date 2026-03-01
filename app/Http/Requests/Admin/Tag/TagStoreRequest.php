<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Tag;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TagStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:1', 'max:50', Rule::unique('tags', 'name')],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Введите название тега',
            'name.unique' => 'Тег с таким названием уже существует',
            'name.max' => 'Название не должно превышать 50 символов',
        ];
    }
}
