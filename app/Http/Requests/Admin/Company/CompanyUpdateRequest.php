<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Company;

use Illuminate\Foundation\Http\FormRequest;

final class CompanyUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'public_name' => ['sometimes', 'string', 'min:1', 'max:255'],
            'legal_name' => ['sometimes', 'string', 'min:1', 'max:255'],
            'description' => ['sometimes', 'string', 'min:10', 'max:5000'],
            'phone' => ['sometimes', 'string', 'max:50'],
            'email' => ['sometimes', 'email', 'max:255', 'unique:companies,email,'.$this->company?->id],
            'site_url' => ['nullable', 'url', 'max:255'],
            'instagram' => ['nullable', 'string', 'max:255'],
            'telegram' => ['nullable', 'string', 'max:255'],
            'stand_code' => ['sometimes', 'integer', 'min:1'],
            'stand_area' => ['sometimes', 'integer', 'min:1'],
            'power_kw' => ['sometimes', 'integer', 'min:1'],
            'storage_enabled' => ['sometimes', 'boolean'],
            'show_on_site' => ['sometimes', 'boolean'],
            'activities' => ['nullable', 'string', 'max:5000'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:tags,id'],
            'logo' => ['nullable', 'image', 'max:10240'],
            'logo_alt' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'Компания с таким email уже существует',
            'logo.image' => 'Логотип должен быть изображением',
            'logo.max' => 'Размер логотипа не должен превышать 10MB',
        ];
    }
}
