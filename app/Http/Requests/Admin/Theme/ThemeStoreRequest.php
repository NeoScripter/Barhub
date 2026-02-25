<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Theme;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ThemeStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:1', 'max:50', Rule::unique('themes', 'name')],
            'color_hex' => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Введите название направления',
            'name.unique' => 'Направление с таким названием уже существует',
            'name.max' => 'Название не должно превышать 50 символов',
            'color_hex.required' => 'Выберите цвет',
            'color_hex.regex' => 'Неверный формат цвета',
        ];
    }
}
