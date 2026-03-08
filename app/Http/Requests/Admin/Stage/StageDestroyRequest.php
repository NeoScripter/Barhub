<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Stage;

use Illuminate\Foundation\Http\FormRequest;

final class StageDestroyRequest extends FormRequest
{
    public function authorize(): bool
    {
        $stage = $this->route('stage');

        return ! $stage->events()->exists();
    }

    public function rules(): array
    {
        return [];
    }

    protected function failedAuthorization()
    {
        abort(403, 'Невозможно удалить площадку, так как она используется в событиях');
    }
}
