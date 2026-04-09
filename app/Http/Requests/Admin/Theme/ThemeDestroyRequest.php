<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Theme;

use Illuminate\Foundation\Http\FormRequest;

final class ThemeDestroyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }
}
