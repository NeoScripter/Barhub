<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\CompanyFollowup;

use Illuminate\Foundation\Http\FormRequest;

final class CompanyFollowupUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'min:1', 'max:200'],
            'description' => ['sometimes', 'string', 'min:10', 'max:5000'],
            'comment' => ['sometimes', 'string', 'min:1', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return (new CompanyFollowupStoreRequest())->messages();
    }
}
