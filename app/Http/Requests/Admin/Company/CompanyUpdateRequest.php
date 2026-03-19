<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Company;

use Illuminate\Foundation\Http\FormRequest;

final class CompanyUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        $exhibition = $this->company->exhibition;
        return $this->user()->can('view', $exhibition);
    }

    public function rules(): array
    {
        return [
            'public_name' => ['sometimes', 'string', 'min:1', 'max:255'],
            'legal_name' => ['sometimes', 'string', 'min:1', 'max:255'],
            'stand_area' => ['sometimes', 'integer', 'min:1'],
            'stand_code' => ['sometimes', 'string', 'min:1'],
            'power_kw' => ['sometimes', 'integer', 'min:1'],
            'storage_enabled' => ['sometimes', 'boolean'],
            'show_on_site' => ['sometimes', 'boolean'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:tags,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'public_name.string'   => 'Публичное название компании должно быть строкой',
            'public_name.max'      => 'Публичное название не должно превышать 255 символов',

            'legal_name.string'    => 'Юридическое название компании должно быть строкой',
            'legal_name.max'       => 'Юридическое название не должно превышать 255 символов',

            'stand_code.string'    => 'Номер стенда должен быть строкой',
            'stand_code.min'       => 'Номер стенда не может быть пустым',

            'stand_area.integer'   => 'Площадь стенда должна быть числом',
            'stand_area.min'       => 'Площадь стенда должна быть не менее 1',

            'power_kw.integer'     => 'Мощность должна быть числом',
            'power_kw.min'         => 'Мощность должна быть не менее 1',

            'storage_enabled.boolean'  => 'Значение склада должно быть булевым',

            'show_on_site.boolean'     => 'Значение отображения на сайте должно быть булевым',
        ];
    }
}
