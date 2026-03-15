import FormButtons from '@/components/form/FormButtons';
import InputError from '@/components/form/InputError';
import AccentHeading from '@/components/ui/AccentHeading';
import FileInput from '@/components/ui/FileInput';
import { Input } from '@/components/ui/Input';
import { Label } from '@/components/ui/Label';
import { Textarea } from '@/components/ui/Textarea';
import { index, store } from '@/wayfinder/routes/admin/tasks';
import { Inertia } from '@/wayfinder/types';
import { router, useForm } from '@inertiajs/react';
import { FC } from 'react';
import { toast } from 'sonner';

const Create: FC<Inertia.Pages.Admin.Tasks.Create> = ({
    exhibition,
    company,
}) => {
    const { data, setData, post, processing, errors } = useForm({
        title: '',
        description: '',
        deadline: '',
        file: null as File | null,
        file_name: '',
        comment: '',
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(store({ exhibition, company }).url, {
            preserveScroll: false,
            onSuccess: () => {
                router.visit(index({ exhibition, company }));
                toast.success('Задача успешно создана');
            },
        });
    };

    const hasComment = data.comment != null && data.comment !== '';

    return (
        <div className="mx-auto w-full max-w-250">
            <div className="mb-8 text-center md:mb-12">
                <AccentHeading
                    asChild
                    className="mb-1 text-lg text-secondary"
                >
                    <h2>Создать задачу</h2>
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
                            type="text"
                            required
                            name="title"
                            value={data.title}
                            onChange={(e) => setData('title', e.target.value)}
                            placeholder="Введите название задачи"
                        />
                        <InputError message={errors.title} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="description">Описание</Label>
                        <Textarea
                            id="description"
                            required
                            name="description"
                            value={data.description}
                            onChange={(e) =>
                                setData('description', e.target.value)
                            }
                            className="max-w-full"
                            placeholder="Введите описание задачи"
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

                    {hasComment && (
                        <>
                            <div className="grid gap-2">
                                <Label htmlFor="file">
                                    Прикрепить новый файл
                                </Label>
                                <FileInput
                                    isEdited={true}
                                    id="file"
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

                    <div className="grid gap-2">
                        <Label htmlFor="comment">Комментарий</Label>
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
                </div>

                <FormButtons
                    label="Создать"
                    processing={processing}
                    backUrl={index({ exhibition, company }).url}
                />
            </form>
        </div>
    );
};

export default Create;
