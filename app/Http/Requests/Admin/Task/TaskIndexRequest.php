<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Exhibition;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class TaskIndexRequest extends FormRequest
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
                    'deadline',
                    '-deadline',
                    'status',
                    '-status',
                ]),
            ],
            'page' => ['sometimes', 'integer', 'min:1'],
        ];
    }
}
