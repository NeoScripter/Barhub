<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\InfoItem;

use Illuminate\Foundation\Http\FormRequest;

final class InfoItemUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'min:1', 'max:255'],
            'url'   => ['sometimes', 'required', 'string', 'url', 'max:2048'],
            'image' => ['nullable', 'image', 'max:10240', 'mimes:jpg,jpeg,png,gif,webp,svg'],
        ];
    }
    public function messages(): array
    {
        return (new InfoItemStoreRequest())->messages();
    }
}
