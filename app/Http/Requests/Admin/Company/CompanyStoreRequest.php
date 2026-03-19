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
            'legal_name' => ['required', 'string', 'min:1', 'max:255'],
            'stand_code' => ['required', 'string', 'min:1'],
            'stand_area' => ['required', 'integer', 'min:1'],
            'power_kw' => ['required', 'integer', 'min:1'],
            'storage_enabled' => ['required', 'boolean'],
            'show_on_site' => ['required', 'boolean'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:tags,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'public_name.required' => 'Введите публичное название компании',
            'public_name.string'   => 'Публичное название компании должно быть строкой',
            'public_name.max'      => 'Публичное название не должно превышать 255 символов',

            'legal_name.required'  => 'Введите юридическое название компании',
            'legal_name.string'    => 'Юридическое название компании должно быть строкой',
            'legal_name.max'       => 'Юридическое название не должно превышать 255 символов',

            'stand_code.required'  => 'Введите номер стенда',
            'stand_code.string'    => 'Номер стенда должен быть строкой',
            'stand_code.min'       => 'Номер стенда не может быть пустым',

            'stand_area.required'  => 'Введите площадь стенда',
            'stand_area.integer'   => 'Площадь стенда должна быть числом',
            'stand_area.min'       => 'Площадь стенда должна быть не менее 1',

            'power_kw.required'    => 'Введите мощность в кВт',
            'power_kw.integer'     => 'Мощность должна быть числом',
            'power_kw.min'         => 'Мощность должна быть не менее 1',

            'storage_enabled.required' => 'Укажите наличие склада',
            'storage_enabled.boolean'  => 'Значение склада должно быть булевым',

            'show_on_site.required'    => 'Укажите отображение на сайте',
            'show_on_site.boolean'     => 'Значение отображения на сайте должно быть булевым',
        ];
    }
}
