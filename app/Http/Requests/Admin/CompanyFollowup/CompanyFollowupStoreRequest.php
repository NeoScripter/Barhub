<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\CompanyFollowup;

use Illuminate\Foundation\Http\FormRequest;

final class CompanyFollowupStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:1', 'max:200'],
            'description' => ['required', 'string', 'min:10', 'max:5000'],
            'comment' => ['required', 'string', 'min:10', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Введите название',
            'name.min' => 'Название должно содержать не менее 1 символа',
            'name.max' => 'Название не должно превышать 200 символов',
            'description.required' => 'Введите описание',
            'description.min' => 'Описание должно содержать не менее 10 символов',
            'description.max' => 'Описание не должно превышать 5000 символов',
            'comment.required' => 'Введите комментарий',
            'comment.min' => 'Комментарий должен содержать не менее 10 символов',
            'comment.max' => 'Длина комментария не должна превышать 5000 символов',
        ];
    }
}
