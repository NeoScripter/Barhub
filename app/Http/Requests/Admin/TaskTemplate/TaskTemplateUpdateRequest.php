<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\TaskTemplate;

use Illuminate\Foundation\Http\FormRequest;

final class TaskTemplateUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'min:1', 'max:100'],
            'description' => ['sometimes', 'required', 'string', 'min:10', 'max:5000'],
            'deadline' => ['sometimes', 'required', 'date', 'after:now'],
            'file_url' => [
                'nullable',
                'file',
                'max:10240',
                'mimes:jpg,jpeg,png,gif,webp,svg,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,csv,zip,rar',
            ],
            'file_name' => ['required_with:file', 'nullable', 'string', 'max:255'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return (new TaskTemplateStoreRequest())->messages();
    }
}
