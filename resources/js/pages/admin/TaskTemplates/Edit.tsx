import FormButtons from '@/components/form/FormButtons';
import InputError from '@/components/form/InputError';
import AccentHeading from '@/components/ui/AccentHeading';
import { Button } from '@/components/ui/Button';
import { DeleteAlertDialog } from '@/components/ui/DeleteAlertDialog';
import FileInput from '@/components/ui/FileInput';
import { Input } from '@/components/ui/Input';
import { Label } from '@/components/ui/Label';
import { Textarea } from '@/components/ui/Textarea';
import { convertDateToInputString } from '@/lib/utils';
import {
    destroy,
    index,
    update,
} from '@/wayfinder/routes/admin/exhibitions/tasks';
import { Inertia } from '@/wayfinder/types';
import { router, useForm } from '@inertiajs/react';
import { Trash2 } from 'lucide-react';
import { FC, useState } from 'react';
import { toast } from 'sonner';

const Edit: FC<Inertia.Pages.Admin.Tasks.Edit> = ({
    exhibition,
    company,
    task,
}) => {
    const comment = task.comments?.[0] ?? null;

    const { data, setData, post, processing, errors } = useForm({
        _method: 'put',
        title: task.title,
        description: task.description,
        deadline: convertDateToInputString(task.deadline),
        file: null,
        file_name: comment?.file?.name ?? '',
        comment: comment?.content ?? '',
    });

    const [isDeleting, setIsDeleting] = useState(false);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(update({ exhibition, company, task }).url, {
            preserveScroll: false,
            onSuccess: () => {
                toast.success('Задача успешно обновлена');
            },
        });
    };

    const handleDelete = () => {
        setIsDeleting(true);
        router.delete(destroy({ exhibition, company, task }).url, {
            onSuccess: () => {
                toast.success('Задача успешно удалена');
            },
            onError: () => {
                toast.error('Ошибка при удалении задачи');
                setIsDeleting(false);
            },
        });
    };

    const hasComment = data.comment != null && data.comment !== '';

    return (
        <div className="mx-auto w-full max-w-250">
            <div className="mb-8 flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
                <DeleteAlertDialog
                    trigger={
                        <Button
                            variant="destructive"
                            type="button"
                            data-test="delete-task"
                        >
                            Удалить задачу
                            <Trash2 />
                        </Button>
                    }
                    title="Удалить задачу?"
                    description={`Вы уверены, что хотите удалить задачу "${task.title}"? Это действие нельзя отменить.`}
                    onConfirm={handleDelete}
                    confirmText="Удалить"
                    cancelText="Отмена"
                    isLoading={isDeleting}
                />
            </div>
            <div className="mb-8 text-center md:mb-12">
                <AccentHeading
                    asChild
                    className="mb-1 text-lg text-secondary"
                >
                    <h2>Редактировать задачу</h2>
                </AccentHeading>
            </div>
            <form
                onSubmit={handleSubmit}
                className="flex flex-col gap-6"
                encType="multipart/form-data"
            >
                <div className="grid gap-6">
                    <div className="grid gap-2">
                        <Label htmlFor="title">Название</Label>
                        <Input
                            id="title"
                            name="title"
                            type="text"
                            value={data.title}
                            onChange={(e) => setData('title', e.target.value)}
                        />
                        <InputError message={errors.title} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="description">Описание</Label>
                        <Textarea
                            id="description"
                            name="description"
                            value={data.description}
                            onChange={(e) =>
                                setData('description', e.target.value)
                            }
                            className="max-w-full"
                        />
                        <InputError message={errors.description} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="deadline">Срок выполнения</Label>
                        <Input
                            id="deadline"
                            type="datetime-local"
                            name="deadline"
                            value={data.deadline}
                            onChange={(e) =>
                                setData('deadline', e.target.value)
                            }
                        />
                        <InputError message={errors.deadline} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="comment">Добавить комментарий</Label>
                        <Textarea
                            id="comment"
                            name="comment"
                            value={data.comment}
                            onChange={(e) => setData('comment', e.target.value)}
                            className="max-w-full"
                            placeholder="Введите комментарий"
                        />
                        <InputError message={errors.comment} />
                    </div>

                    {hasComment && (
                        <>
                            <div className="grid gap-2">
                                <Label htmlFor="file">
                                    Прикрепить новый файл
                                </Label>
                                <FileInput
                                    isEdited={true}
                                    id="file"
                                    src={comment?.file?.url}
                                    filename={comment?.file?.name}
                                    error={errors.file}
                                    onChange={(file) => {
                                        setData('file', file);
                                        if (file)
                                            setData('file_name', file.name);
                                    }}
                                />
                                <InputError message={errors.file} />
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="file_name">
                                    Название файла
                                </Label>
                                <Input
                                    id="file_name"
                                    type="text"
                                    name="file_name"
                                    value={data.file_name}
                                    onChange={(e) =>
                                        setData('file_name', e.target.value)
                                    }
                                    placeholder="Введите название файла"
                                />
                                <InputError message={errors.file_name} />
                            </div>
                        </>
                    )}
                </div>

                <FormButtons
                    label="Сохранить"
                    processing={processing}
                    backUrl={index({ exhibition, company }).url}
                />
            </form>
        </div>
    );
};

export default Edit;
