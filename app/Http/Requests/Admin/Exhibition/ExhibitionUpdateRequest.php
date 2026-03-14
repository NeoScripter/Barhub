<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Exhibition;

use Illuminate\Foundation\Http\FormRequest;

final class ExhibitionUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'               => ['sometimes', 'required', 'string', 'max:255'],
            'starts_at'          => ['sometimes', 'required', 'date'],
            'ends_at'            => ['sometimes', 'required', 'date', 'after:starts_at'],
            'location'           => ['sometimes', 'required', 'string', 'max:255'],
            'buildin_folder_url' => ['sometimes', 'required', 'string', 'url', 'max:2048'],
            'is_active'          => ['sometimes', 'required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'               => 'Название обязательно для заполнения',
            'name.max'                    => 'Название не должно превышать 255 символов',
            'starts_at.required'          => 'Дата начала обязательна для заполнения',
            'starts_at.date'              => 'Введите корректную дату начала',
            'ends_at.required'            => 'Дата окончания обязательна для заполнения',
            'ends_at.date'                => 'Введите корректную дату окончания',
            'ends_at.after'               => 'Дата окончания должна быть позже даты начала',
            'location.required'           => 'Местоположение обязательно для заполнения',
            'buildin_folder_url.required' => 'Ссылка на папку обязательна для заполнения',
            'buildin_folder_url.url'      => 'Введите корректный URL папки',
            'is_active.required'          => 'Статус активности обязателен для заполнения',
            'is_active.boolean'           => 'Статус активности должен быть булевым значением',
        ];
    }
}
