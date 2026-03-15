<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Followup;

use Illuminate\Foundation\Http\FormRequest;

final class FollowupUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        $exhibition = $this->service?->company?->exhibition;
        return $this->user()->can('view', $exhibition);
    }

    public function rules(): array
    {
        return [
            'is_accepted' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'is_accepted.required' => 'Данное значение должно присутсвовать',
        ];
    }
}
