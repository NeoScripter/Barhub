<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Followup;

use Illuminate\Foundation\Http\FormRequest;

final class FollowupUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'is_accepted' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'is_accepted.required' => 'Данное значение должно присутсвовать',
        ];
    }
}
