<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Service;

use Illuminate\Foundation\Http\FormRequest;

final class ServiceStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:1', 'max:200'],
            'description' => ['required', 'string', 'min:10', 'max:5000'],
            'placeholder' => ['required', 'string', 'min:10', 'max:5000'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Введите название',
            'name.min' => 'Название должно содержать не менее 1 символа',
            'name.max' => 'Название не должно превышать 200 символов',
            'description.required' => 'Введите описание',
            'description.min' => 'Описание должно содержать не менее 10 символов',
            'description.max' => 'Описание не должно превышать 5000 символов',
            'placeholder.required' => 'Введите подсказку',
            'placeholder.min' => 'Подсказка должна содержать не менее 10 символов',
            'placeholder.max' => 'Длинна подсказки не должна превышать 5000 символов',
            'is_active.required' => 'Укажите статус активности',
            'is_active.boolean' => 'Статус активности может быть только да или нет',
        ];
    }
}
