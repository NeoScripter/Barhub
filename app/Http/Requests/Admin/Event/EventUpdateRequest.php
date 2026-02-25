<?php

namespace App\Http\Requests\Admin\Event;

use App\Enums\PersonRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EventUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'min:1', 'max:255'],
            'description' => ['sometimes', 'string', 'min:10', 'max:5000'],
            'stage_id' => ['sometimes', 'nullable', 'exists:stages,id'],
            'theme_ids' => ['sometimes', 'array'],
            'theme_ids.*' => ['exists:themes,id'],
            'people' => ['sometimes', 'array'],
            'people.*.person_id' => ['required', 'exists:people,id'],
            'people.*.role' => [
                'required',
                'integer',
                Rule::in(collect(PersonRole::cases())->pluck('value')->toArray())
            ],
            'starts_at' => ['sometimes', 'date'],
            'ends_at' => ['sometimes', 'date', 'after:starts_at'],
        ];
    }
}
