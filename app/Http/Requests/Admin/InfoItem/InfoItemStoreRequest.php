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
            'alt' => ['required_with:image', 'nullable', 'string', 'min:1', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Название обязательно для заполнения',
            'title.max'      => 'Название не должно превышать 255 символов',
            'url.required'   => 'Ссылка обязательна для заполнения',
            'url.url'        => 'Введите корректный URL',
            'url.max'        => 'Ссылка не должна превышать 2048 символов',
            'image.image'    => 'Файл должен быть изображением',
            'image.max'      => 'Размер изображения не должен превышать 10 МБ',
            'image.mimes'    => 'Изображение должно быть в формате jpg, jpeg, png, gif, webp или svg',
            'alt.required' => 'Альтернативный текст к фото обязателен для заполнения',
            'alt.max'      => 'Альтернативный текст к фото не должен превышать 255 символов',
        ];
    }
}
