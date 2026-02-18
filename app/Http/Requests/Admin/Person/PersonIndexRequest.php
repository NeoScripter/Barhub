<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Person;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class PersonIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('view', $this->exhibition);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'sort' => [
                'sometimes',
                'string',
                Rule::in([
                    'name',
                    '-name',
                ]),
            ],
            'search' => ['sometimes', 'string', 'max:50'],
            'page' => ['sometimes', 'integer', 'min:1'],
        ];
    }
}
