<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Theme;

use Illuminate\Foundation\Http\FormRequest;

class ThemeDestroyRequest extends FormRequest
{
    public function authorize(): bool
    {
        $theme = $this->route('theme');

        if ($theme->events()->exists()) {
            return false;
        }

        return true;
    }

    public function rules(): array
    {
        return [];
    }

    protected function failedAuthorization()
    {
        abort(403, 'Невозможно удалить направление, так как оно используется в событиях');
    }
}
