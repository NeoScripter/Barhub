<?php

namespace App\Http\Requests\Exhibition;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExhibitionUpdateRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'               => ['sometimes', 'string', 'max:255'],
            'slug'               => ['sometimes', 'string', 'max:255', Rule::unique('exhibitions')->ignore($this->exhibition)],
            'starts_at'          => ['sometimes', 'date'],
            'ends_at'            => ['sometimes', 'date', 'after:starts_at'],
            'location'           => ['sometimes', 'string', 'max:255'],
            'buildin_folder_url' => ['sometimes', 'string', 'url', 'max:255'],
            'is_active'          => ['sometimes', 'boolean'],
        ];
    }
}
