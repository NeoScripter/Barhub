<?php

declare(strict_types=1);

namespace App\Http\Requests\Exponent\Task;

use App\Http\Requests\Admin\Task\TaskStoreRequest;
use Illuminate\Foundation\Http\FormRequest;

final class TaskUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'comment' => trim($this->comment),
        ]);
    }

    public function rules(): array
    {
        return [
            'file' => [
                'nullable',
                'file',
                'max:51200',
                'mimes:jpg,jpeg,png,gif,webp,svg,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,csv,zip,rar',
            ],
            'file_name' => ['required_with:file', 'nullable', 'string', 'max:255'],
            'comment' => ['required', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'file.file' => 'Загруженный объект должен быть файлом',
            'file.max' => 'Размер файла не должен превышать 10 МБ',
            'file.mimes' => 'Допустимые форматы: изображения, PDF, Word, Excel, PowerPoint, текстовые файлы, CSV, ZIP, RAR',

            'file_name.required_with' => 'Укажите название файла',
            'file_name.max' => 'Название файла не должно превышать 255 символов',

            'comment.required' => 'Введите комментарий',
            'comment.max' => 'Комментарий не должен превышать 2000 символов',
        ];
    }
}
