<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class ExhibitionIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sort' => [
                'sometimes',
                'string',
                Rule::in([
                    'name',
                    '-name',
                    'starts_at',
                    '-starts_at',
                    'ends_at',
                    '-ends_at',
                    'location',
                    '-location',
                    'is_active',
                    '-is_active',
                ]),
            ],
            'page' => ['sometimes', 'integer', 'min:1'],
        ];
    }
}
