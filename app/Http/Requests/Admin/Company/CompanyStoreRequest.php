<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Company;

use Illuminate\Foundation\Http\FormRequest;

final class CompanyStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'public_name' => ['required', 'string', 'min:1', 'max:255'],
            'legal_name' => ['nullable', 'string', 'min:1', 'max:255'],
            'description' => ['nullable', 'string', 'min:10', 'max:5000'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255', 'unique:companies,email'],
            'site_url' => ['nullable', 'url', 'max:255'],
            'instagram' => ['nullable', 'string', 'max:255'],
            'telegram' => ['nullable', 'string', 'max:255'],
            'stand_code' => ['required', 'string', 'min:1'],
            'stand_area' => ['required', 'integer', 'min:1'],
            'power_kw' => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'storage_enabled' => ['required', 'boolean'],
            'show_on_site' => ['required', 'boolean'],
            'activities' => ['nullable', 'string', 'max:5000'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:tags,id'],
            'logo' => ['nullable', 'image', 'max:51200'],
            'logo_alt' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'public_name.required' => 'Введите публичное название компании',
            'public_name.string'   => 'Публичное название должно быть строкой',
            'public_name.min'      => 'Публичное название должно содержать не менее 1 символа',
            'public_name.max'      => 'Публичное название не должно превышать 255 символов',

            'legal_name.required'  => 'Введите юридическое название компании',
            'legal_name.string'    => 'Юридическое название должно быть строкой',
            'legal_name.min'       => 'Юридическое название должно содержать не менее 1 символа',
            'legal_name.max'       => 'Юридическое название не должно превышать 255 символов',

            'description.required' => 'Введите описание компании',
            'description.string'   => 'Описание должно быть строкой',
            'description.min'      => 'Описание должно содержать не менее 10 символов',
            'description.max'      => 'Описание не должно превышать 5000 символов',

            'phone.required'       => 'Введите телефон',
            'phone.string'         => 'Телефон должен быть строкой',
            'phone.max'            => 'Телефон не должен превышать 50 символов',

            'email.required'       => 'Введите email',
            'email.email'          => 'Введите корректный email',
            'email.max'            => 'Email не должен превышать 255 символов',
            'email.unique'         => 'Компания с таким email уже существует',

            'site_url.url'         => 'Введите корректный URL сайта',
            'site_url.max'         => 'URL сайта не должен превышать 255 символов',

            'instagram.string'     => 'Instagram должен быть строкой',
            'instagram.max'        => 'Instagram не должен превышать 255 символов',

            'telegram.string'      => 'Telegram должен быть строкой',
            'telegram.max'         => 'Telegram не должен превышать 255 символов',

            'stand_code.required'  => 'Введите номер стенда',
            'stand_code.min'       => 'Номер стенда должен быть не менее 1',

            'stand_area.required'  => 'Введите площадь стенда',
            'stand_area.integer'   => 'Площадь стенда должна быть числом',
            'stand_area.min'       => 'Площадь стенда должна быть не менее 1',

            'power_kw.required'    => 'Введите мощность в кВт',
            'power_kw.numeric'     => 'Мощность должна быть числом',
            'power_kw.min'         => 'Мощность должна быть не менее 1',

            'storage_enabled.required' => 'Укажите наличие склада',
            'storage_enabled.boolean'  => 'Значение склада должно быть булевым',

            'show_on_site.required'    => 'Укажите отображение на сайте',
            'show_on_site.boolean'     => 'Значение отображения на сайте должно быть булевым',

            'activities.string'    => 'Деятельность компании должна быть строкой',
            'activities.max'       => 'Деятельность компании не должна превышать 5000 символов',

            'tags.array'           => 'Теги должны быть массивом',
            'tags.*.integer'       => 'Идентификатор тега должен быть числом',
            'tags.*.exists'        => 'Выбранный тег не существует',

            'logo.image'           => 'Логотип должен быть изображением',
            'logo.max'             => 'Размер логотипа не должен превышать 50MB',

            'logo_alt.string'      => 'Описание логотипа должно быть строкой',
            'logo_alt.max'         => 'Описание логотипа не должно превышать 255 символов',
        ];
    }
}
