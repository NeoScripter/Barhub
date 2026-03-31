import FormButtons from '@/components/form/FormButtons';
import InputError from '@/components/form/InputError';
import AccentHeading from '@/components/ui/AccentHeading';
import DownloadFileLink from '@/components/ui/DownloadFileLink';
import FileInput from '@/components/ui/FileInput';
import { Input } from '@/components/ui/Input';
import { Label } from '@/components/ui/Label';
import LabeledContent from '@/components/ui/LabeledContent';
import { Textarea } from '@/components/ui/Textarea';
import { formatDateAndTime } from '@/lib/utils';
import { index, update } from '@/wayfinder/routes/exponent/tasks';
import { Inertia } from '@/wayfinder/types';
import { useForm } from '@inertiajs/react';
import React, { FC } from 'react';
import { toast } from 'sonner';

const Edit: FC<Inertia.Pages.Admin.Tasks.Edit> = ({ task }) => {
    const { data, setData, post, processing, errors } = useForm({
        _method: 'put',
        file: null,
        file_name: '',
        comment: '',
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(update({ task }).url, {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Задача успешно отправлена на проверку');
            },
        });
    };

    console.log(task.comments)

    return (
        <div className="mx-auto w-full max-w-250">
            <div className="mb-8 text-center md:mb-12">
                <AccentHeading
                    asChild
                    className="mb-1 text-lg text-secondary"
                >
                    <h2>Ответ на задачу</h2>
                </AccentHeading>
            </div>

            <div className="mb-6 flex flex-col gap-6">
                <LabeledContent label="Название задачи">
                    <p>{task.title}</p>
                </LabeledContent>
                <LabeledContent label="Описание задачи">
                    <p className="whitespace-pre-line">{task.description}</p>
                </LabeledContent>
                <LabeledContent label="Комментарий">
                    {task.comments?.map((comment) => (
                        <div
                            className="mb-3"
                            key={comment.id}
                        >
                            {comment.user && (
                                <small className="mb-3 block">
                                    {comment.user?.name}
                                </small>
                            )}
                            <small className="mb-3 block">
                                {formatDateAndTime(
                                    new Date(comment.created_at),
                                )}
                            </small>
                            {comment.content && <p>{comment.content}</p>}
                            {comment.file && (
                                <DownloadFileLink
                                    className="my-4"
                                    href={comment.file?.url}
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    filename={comment.file?.name}
                                />
                            )}
                        </div>
                    ))}
                </LabeledContent>
            </div>

            <form
                className="flex flex-col gap-6"
                onSubmit={handleSubmit}
            >
                <div className="grid gap-2">
                    <Label htmlFor="comment">Добавить комментарий</Label>
                    <Textarea
                        id="comment"
                        name="comment"
                        value={data.comment}
                        onChange={(e) => setData('comment', e.target.value)}
                        className="max-w-2xl"
                        placeholder="Введите комментарий"
                    />
                    <InputError message={errors.comment} />
                </div>

                <div className="grid gap-2">
                    <Label htmlFor="file">Прикрепить новый файл</Label>
                    <FileInput
                        isEdited={true}
                        id="file"
                        filename={data.file_name}
                        error={errors.file}
                        onChange={(file) => {
                            setData('file', file);
                            if (file) setData('file_name', file.name);
                        }}
                    />
                    <InputError message={errors.file} />
                </div>
                <div className="grid gap-2">
                    <Label htmlFor="file_name">Название файла</Label>
                    <Input
                        id="file_name"
                        type="text"
                        name="file_name"
                        value={data.file_name}
                        onChange={(e) => setData('file_name', e.target.value)}
                        placeholder="Введите название файла"
                    />
                    <InputError message={errors.file_name} />
                </div>

                <FormButtons
                    label="Отправить на проверку"
                    processing={processing}
                    backUrl={index().url}
                />
            </form>
        </div>
    );
};

export default Edit;
