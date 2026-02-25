<?php
declare(strict_types=1);

namespace App\Http\Requests\Admin\Event;

use App\Enums\PersonRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EventStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:1', 'max:255'],
            'description' => ['required', 'string', 'min:10', 'max:5000'],
            'stage_id' => ['nullable', 'exists:stages,id'],
            'theme_ids' => ['nullable', 'array'],
            'theme_ids.*' => ['exists:themes,id'],
            'people' => ['nullable', 'array'],
            'people.*.person_id' => ['required', 'exists:people,id'],
            'people.*.roles' => ['required', 'array', 'min:1'],
            'people.*.roles.*' => [
                'required',
                'integer',
                Rule::in(collect(PersonRole::cases())->pluck('value')->toArray())
            ],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Введите название события',
            'description.required' => 'Введите описание события',
            'starts_at.required' => 'Выберите время начала',
            'ends_at.required' => 'Выберите время окончания',
            'people.*.person_id.required' => 'Выберите участника',
            'people.*.person_id.exists' => 'Выбранный участник не существует',
            'people.*.roles.required' => 'Выберите хотя бы одну роль',
            'people.*.roles.min' => 'Выберите хотя бы одну роль',
            'people.*.roles.*.in' => 'Недопустимое значение роли',
            'ends_at.after' => 'Время окончания должно быть позже времени начала',
        ];
    }
}
