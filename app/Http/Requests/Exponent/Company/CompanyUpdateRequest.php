<?php

declare(strict_types=1);

namespace App\Http\Requests\Exponent\Company;

use Illuminate\Foundation\Http\FormRequest;

final class CompanyUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->company->id === $this->company->id;
    }

    public function rules(): array
    {
        return [
            'public_name' => ['sometimes', 'string', 'min:1', 'max:255'],
            'description' => ['sometimes', 'string', 'min:10', 'max:5000'],
            'phone' => ['sometimes', 'string', 'max:50'],
            'email' => ['sometimes', 'email', 'max:255', 'unique:companies,email,' . $this->company?->id],
            'site_url' => ['nullable', 'url', 'max:255'],
            'instagram' => ['nullable', 'string', 'max:255'],
            'telegram' => ['nullable', 'string', 'max:255'],
            'activities' => ['nullable', 'string', 'max:5000'],
            'logo' => ['nullable', 'image', 'max:51200'],
        ];
    }

    public function messages(): array
    {
        return [
            'public_name.string'   => 'Название компании должно быть строкой',
            'public_name.min'      => 'Название компании должно содержать не менее 1 символа',
            'public_name.max'      => 'Название компании не должно превышать 255 символов',

            'description.string'   => 'Описание должно быть строкой',
            'description.min'      => 'Описание должно содержать не менее 10 символов',
            'description.max'      => 'Описание не должно превышать 5000 символов',

            'phone.string'         => 'Телефон должен быть строкой',
            'phone.max'            => 'Телефон не должен превышать 50 символов',

            'email.email'          => 'Введите корректный email',
            'email.max'            => 'Email не должен превышать 255 символов',
            'email.unique'         => 'Компания с таким email уже существует',

            'site_url.url'         => 'Введите корректный URL сайта',
            'site_url.max'         => 'URL сайта не должен превышать 255 символов',

            'instagram.string'     => 'Instagram должен быть строкой',
            'instagram.max'        => 'Instagram не должен превышать 255 символов',

            'telegram.string'      => 'Telegram должен быть строкой',
            'telegram.max'         => 'Telegram не должен превышать 255 символов',

            'activities.string'    => 'Деятельность компании должна быть строкой',
            'activities.max'       => 'Деятельность компании не должна превышать 5000 символов',

            'logo.image'           => 'Логотип должен быть изображением',
            'logo.max'             => 'Размер логотипа не должен превышать 50MB',

            'logo_alt.string'      => 'Описание логотипа должно быть строкой',
            'logo_alt.max'         => 'Описание логотипа не должно превышать 255 символов',
        ];
    }
}
