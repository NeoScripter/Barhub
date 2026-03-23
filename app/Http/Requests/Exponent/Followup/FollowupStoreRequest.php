<?php

declare(strict_types=1);

namespace App\Http\Requests\Exponent\Followup;

use Illuminate\Foundation\Http\FormRequest;

final class FollowupStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'service_id' => ['required', 'exists:services,id'],
            'comment' => ['required', 'string', 'min:10', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'comment.required' => 'Введите комментарий',
            'comment.min' => 'Комментарий должен содержать не менее 10 символов',
            'comment.max' => 'Длина комментария не должна превышать 5000 символов',
        ];
    }
}
