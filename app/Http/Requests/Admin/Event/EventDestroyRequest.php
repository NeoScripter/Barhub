<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Event;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

final class EventDestroyRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if ($user->role === UserRole::SUPER_ADMIN) {
            return true;
        }

        $userExhibitionIds = $user->exhibitions()->pluck('exhibitions.id')->toArray();

        return in_array($this->event->exhibition->id, $userExhibitionIds);
    }
}
