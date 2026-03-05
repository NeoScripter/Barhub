<?php

declare(strict_types=1);
namespace App\Http\Requests\Admin\Task;
use Illuminate\Foundation\Http\FormRequest;

class TaskStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'min:1', 'max:100'],
            'description' => ['required', 'string', 'min:10', 'max:5000'],
            'deadline'    => ['required', 'date', 'after:now'],
            'file'        => [
                'nullable',
                'file',
                'max:10240',
                'mimes:jpg,jpeg,png,gif,webp,svg,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,csv,zip,rar',
            ],
            'file_name'   => ['required_with:file', 'nullable' ,'string', 'max:255'],
            'comment'     => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'       => 'Введите название задачи',
            'title.min'            => 'Название задачи должно содержать не менее 1 символа',
            'title.max'            => 'Название задачи не должно превышать 100 символов',

            'description.required' => 'Введите описание задачи',
            'description.min'      => 'Описание задачи должно содержать не менее 10 символов',
            'description.max'      => 'Описание задачи не должно превышать 5000 символов',

            'deadline.required'    => 'Укажите срок выполнения задачи',
            'deadline.date'        => 'Срок выполнения должен быть корректной датой',
            'deadline.after'       => 'Срок выполнения должен быть в будущем',

            'file.file'            => 'Загруженный объект должен быть файлом',
            'file.max'             => 'Размер файла не должен превышать 10 МБ',
            'file.mimes'           => 'Допустимые форматы: изображения, PDF, Word, Excel, PowerPoint, текстовые файлы, CSV, ZIP, RAR',

            'file_name.required_with' => 'Укажите название файла',
            'file_name.max'           => 'Название файла не должно превышать 255 символов',

            'comment.max'          => 'Комментарий не должен превышать 2000 символов',
        ];
    }
}
