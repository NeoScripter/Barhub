<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Person;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

final class PersonDeleteRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if ($user->role === UserRole::SUPER_ADMIN || $user->role === UserRole::ADMIN) {
            return true;
        }

        return false;
    }
}
