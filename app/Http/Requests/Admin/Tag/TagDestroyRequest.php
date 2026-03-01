<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Tag;

use Illuminate\Foundation\Http\FormRequest;

class TagDestroyRequest extends FormRequest
{
    public function authorize(): bool
    {
        $tag = $this->route('tag');

        if ($tag->companies()->exists()) {
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
        abort(403, 'Невозможно удалить тег, так как он используется в компаниях');
    }
}
