import InputError from '@/components/form/InputError';
import { Button } from '@/components/ui/Button';
import FileInput from '@/components/ui/FileInput';
import { Input } from '@/components/ui/Input';
import { Label } from '@/components/ui/Label';
import { Spinner } from '@/components/ui/Spinner';
import { Textarea } from '@/components/ui/Textarea';
import { store } from '@/wayfinder/routes/admin/exhibitions/tasks';
import { Inertia } from '@/wayfinder/types';
import { useForm } from '@inertiajs/react';
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
            onSuccess: () => toast.success('Задача успешно создана'),
        });
    };

    return (
        <div className="mx-auto w-full max-w-250">
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
                            placeholder="Введите название задачи"
                        />
                        <InputError message={errors.title} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="description">Описание</Label>
                        <Textarea
                            id="description"
                            required
                            value={data.description}
                            onChange={(e) => setData('description', e.target.value)}
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
                            required
                            value={data.deadline}
                            onChange={(e) => setData('deadline', e.target.value)}
                        />
                        <InputError message={errors.deadline} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="file">Файл</Label>
                        <FileInput
                            isEdited={true}
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
                            value={data.file_name}
                            onChange={(e) => setData('file_name', e.target.value)}
                            placeholder="Введите название файла"
                        />
                        <InputError message={errors.file_name} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="comment">Комментарий</Label>
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
                        Создать
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

export default Create;
