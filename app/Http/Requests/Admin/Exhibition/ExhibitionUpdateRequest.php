<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Exhibition;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class ExhibitionUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->exhibition);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'starts_at' => ['sometimes', 'date'],
            'ends_at' => ['sometimes', 'date', 'after:starts_at'],
            'location' => ['sometimes', 'string', 'max:255'],
            'buildin_folder_url' => ['sometimes', 'string', 'url', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
