<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\InfoItem;

use Illuminate\Foundation\Http\FormRequest;

final class InfoItemStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:1', 'max:255'],
            'url'   => ['required', 'string', 'url', 'max:2048'],
            'image' => ['nullable', 'image', 'max:10240', 'mimes:jpg,jpeg,png,gif,webp,svg'],
        ];
    }
}
