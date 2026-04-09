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
            'image' => ['nullable', 'image', 'max:51200', 'mimes:jpg,jpeg,png,gif,webp,svg'],
            'description' => ['sometimes', 'required', 'string', 'min:10', 'max:5000'],
            'file_url' => [
                'nullable',
                'file',
                'max:51200',
                'mimes:jpg,jpeg,png,gif,webp,svg,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,csv,zip,rar',
            ],
            'file_name' => ['required_with:file', 'nullable', 'string', 'max:255'],
        ];
    }
    public function messages(): array
    {
        return (new InfoItemStoreRequest())->messages();
    }
}
