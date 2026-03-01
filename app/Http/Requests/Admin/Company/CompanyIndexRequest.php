<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Company;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class CompanyIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'sort' => [
                'sometimes',
                'string',
                Rule::in(['public_name', '-public_name']),
            ],
            'search' => ['sometimes', 'string', 'max:50'],
            'page'   => ['sometimes', 'integer', 'min:1'],
        ];
    }
}
