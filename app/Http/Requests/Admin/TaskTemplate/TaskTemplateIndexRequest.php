<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\TaskTemplate;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class TaskTemplateIndexRequest extends FormRequest
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
                ]),
            ],
            'page' => ['sometimes', 'integer', 'min:1'],
        ];
    }
}
