<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

final class EventIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'filter.stage.name' => ['sometimes', 'string', 'max:255'],
            'filter.themes.name' => ['sometimes', 'string', 'max:255'],
            'filter.starts_at' => ['sometimes', 'date'],
        ];
    }
}
