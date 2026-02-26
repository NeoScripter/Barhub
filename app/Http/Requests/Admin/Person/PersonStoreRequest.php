<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Person;

use Illuminate\Foundation\Http\FormRequest;

class PersonStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:1', 'max:255'],
            'regalia' => ['required', 'string', 'min:10', 'max:5000'],
            'bio' => ['required', 'string', 'min:10', 'max:5000'],
            'telegram' => ['nullable', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'max:10240'], // 10MB
            'avatar_alt' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'max:10240'], // 10MB
            'logo_alt' => ['nullable', 'string', 'max:255'],
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
