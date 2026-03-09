<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Task;

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
                    'title',
                    '-title',
                    'deadline',
                    '-deadline',
                    'status',
                    '-status',
                    'company.public_name',
                    '-company.public_name',
                ]),
            ],
            'page' => ['sometimes', 'integer', 'min:1'],
        ];
    }
}
