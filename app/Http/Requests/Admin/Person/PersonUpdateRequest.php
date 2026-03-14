<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Person;

use Illuminate\Foundation\Http\FormRequest;

final class PersonUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'min:1', 'max:255'],
            'regalia' => ['sometimes', 'string', 'min:10', 'max:5000'],
            'bio' => ['sometimes', 'string', 'min:10', 'max:5000'],
            'telegram' => ['nullable', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'max:51200'],
            'logo' => ['nullable', 'image', 'max:51200'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Введите имя участника',
            'regalia.required' => 'Введите регалии участника',
            'bio.required' => 'Введите биографию участника',
            'avatar.image' => 'Аватар должен быть изображением',
            'avatar.max' => 'Размер аватара не должен превышать 10MB',
            'logo.image' => 'Логотип должен быть изображением',
            'logo.max' => 'Размер логотипа не должен превышать 10MB',
        ];
    }
}
