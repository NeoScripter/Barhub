<?php

declare(strict_types=1);
namespace App\Http\Requests\Admin\Service;

use Illuminate\Foundation\Http\FormRequest;

class ServiceUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'name'        => ['sometimes', 'string', 'min:1', 'max:200'],
            'description' => ['sometimes', 'string', 'min:10', 'max:5000'],
            'placeholder' => ['sometimes', 'string', 'min:10', 'max:5000'],
            'is_active'   => ['sometimes', 'boolean'],
        ];
    }
    public function messages(): array
    {
        return (new ServiceStoreRequest())->messages();
    }
}
