<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Stage;

use Illuminate\Foundation\Http\FormRequest;

class StageDestroyRequest extends FormRequest
{
    public function authorize(): bool
    {
        $stage = $this->route('stage');

        if ($stage->events()->exists()) {
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
        abort(403, 'Невозможно удалить площадку, так как она используется в событиях');
    }
}
