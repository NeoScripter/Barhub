<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Followup;

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
                    'name',
                    '-name',
                    'company.public_name',
                    '-company.public_name',
                ]),
            ],
            'page' => ['sometimes', 'integer', 'min:1'],
        ];
    }
}
