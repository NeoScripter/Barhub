<?php

declare(strict_types=1);

namespace App\Http\Requests\Exponent\Followup;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class FollowupIndexRequest extends FormRequest
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
                    'service.name',
                    '-service.name',
                ]),
            ],
            'page' => ['sometimes', 'integer', 'min:1'],
        ];
    }
}
