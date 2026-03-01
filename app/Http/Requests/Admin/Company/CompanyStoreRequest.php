<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Company;

use Illuminate\Foundation\Http\FormRequest;

class CompanyStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'public_name'  => ['required', 'string', 'min:1', 'max:255'],
            'legal_name'   => ['required', 'string', 'min:1', 'max:255'],
            'description'  => ['required', 'string', 'min:10', 'max:5000'],
            'phone'        => ['required', 'string', 'max:50'],
            'email'        => ['required', 'email', 'max:255', 'unique:companies,email'],
            'site_url'     => ['nullable', 'url', 'max:255'],
            'instagram'    => ['nullable', 'string', 'max:255'],
            'telegram'     => ['nullable', 'string', 'max:255'],
            'stand_code'   => ['required', 'integer', 'min:1'],
            'show_on_site' => ['required', 'boolean'],
            'activities'   => ['nullable', 'string', 'max:5000'],
            'tags'         => ['nullable', 'array'],
            'tags.*'       => ['integer', 'exists:tags,id'],
            'logo'         => ['nullable', 'image', 'max:10240'],
            'logo_alt'     => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'public_name.required'  => 'Введите публичное название компании',
            'legal_name.required'   => 'Введите юридическое название компании',
            'description.required'  => 'Введите описание компании',
            'phone.required'        => 'Введите телефон',
            'email.required'        => 'Введите email',
            'email.unique'          => 'Компания с таким email уже существует',
            'stand_code.required'   => 'Введите номер стенда',
            'show_on_site.required' => 'Укажите отображение на сайте',
            'logo.image'            => 'Логотип должен быть изображением',
            'logo.max'              => 'Размер логотипа не должен превышать 10MB',
        ];
    }
}
