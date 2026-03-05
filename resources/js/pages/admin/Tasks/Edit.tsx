import InputError from '@/components/form/InputError';
import { Button } from '@/components/ui/Button';
import { DeleteAlertDialog } from '@/components/ui/DeleteAlertDialog';
import FileInput from '@/components/ui/FileInput';
import { Input } from '@/components/ui/Input';
import { Label } from '@/components/ui/Label';
import { Spinner } from '@/components/ui/Spinner';
import { Textarea } from '@/components/ui/Textarea';
import { convertDateToInputString } from '@/lib/utils';
import { destroy, update } from '@/wayfinder/routes/admin/exhibitions/tasks';
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
    const { data, setData, post, processing, errors } = useForm({
        _method: 'PUT',
        title: task.title,
        description: task.description,
        deadline: convertDateToInputString(task.deadline),
        file: null as File | null,
        file_name: '',
        comment: '',
    });

    const [isDeleting, setIsDeleting] = useState(false);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(update({ exhibition, company, task }).url, {
            forceFormData: true,
            onSuccess: () => toast.success('Задача успешно обновлена'),
        });
    };

    const handleDelete = () => {
        setIsDeleting(true);
        router.delete(destroy({ exhibition, company, task }).url, {
            onSuccess: () => toast.success('Задача успешно удалена'),
            onError: () => {
                toast.error('Ошибка при удалении задачи');
                setIsDeleting(false);
            },
        });
    };

    return (
        <div className="mx-auto w-full max-w-250">
            <div className="mb-8 flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
                <DeleteAlertDialog
                    trigger={
                        <Button
                            variant="destructive"
                            type="button"
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

            <form
                onSubmit={handleSubmit}
                className="flex flex-col gap-6"
            >
                <div className="grid gap-6">
                    <div className="grid gap-2">
                        <Label htmlFor="title">Название</Label>
                        <Input
                            id="title"
                            type="text"
                            required
                            value={data.title}
                            onChange={(e) => setData('title', e.target.value)}
                        />
                        <InputError message={errors.title} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="description">Описание</Label>
                        <Textarea
                            id="description"
                            required
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
                            required
                            value={data.deadline}
                            onChange={(e) =>
                                setData('deadline', e.target.value)
                            }
                        />
                        <InputError message={errors.deadline} />
                    </div>

                    {/* <div className="grid gap-2"> */}
                    {/*     <Label htmlFor="file">Прикрепить новый файл</Label> */}
                    {/*     <FileInput */}
                    {/*         isEdited={true} */}
                    {/*         src={task.file?.url} */}
                    {/*         filename={task.file?.name} */}
                    {/*         error={errors.file} */}
                    {/*         onChange={(file) => { */}
                    {/*             setData('file', file); */}
                    {/*             if (file) setData('file_name', file.name); */}
                    {/*         }} */}
                    {/*     /> */}
                    {/*     <InputError message={errors.file} /> */}
                    {/* </div> */}

                    <div className="grid gap-2">
                        <Label htmlFor="file_name">Название файла</Label>
                        <Input
                            id="file_name"
                            type="text"
                            value={data.file_name}
                            onChange={(e) =>
                                setData('file_name', e.target.value)
                            }
                            placeholder="Введите название файла"
                        />
                        <InputError message={errors.file_name} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="comment">Добавить комментарий</Label>
                        <Textarea
                            id="comment"
                            value={data.comment}
                            onChange={(e) => setData('comment', e.target.value)}
                            className="max-w-full"
                            placeholder="Введите комментарий"
                        />
                        <InputError message={errors.comment} />
                    </div>
                </div>

                <div className="mt-2 flex items-center gap-4">
                    <Button
                        type="submit"
                        className="w-fit"
                        disabled={processing}
                    >
                        {processing && <Spinner />}
                        Сохранить
                    </Button>
                    <Button
                        type="button"
                        variant="tertiary"
                        className="w-fit rounded-md!"
                        onClick={() => history.back()}
                    >
                        Отмена
                    </Button>
                </div>
            </form>
        </div>
    );
};

export default Edit;
