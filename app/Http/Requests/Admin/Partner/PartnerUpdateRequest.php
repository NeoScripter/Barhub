<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Partner;

use Illuminate\Foundation\Http\FormRequest;

final class PartnerUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'is_accepted' => ['required', 'boolean'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'is_accepted.required' => 'Данное значение должно присутсвовать',
        ];
    }
}
