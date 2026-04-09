import FormButtons from '@/components/form/FormButtons';
import InputError from '@/components/form/InputError';
import AccentHeading from '@/components/ui/AccentHeading';
import { Button } from '@/components/ui/Button';
import { DeleteAlertDialog } from '@/components/ui/DeleteAlertDialog';
import FileInput from '@/components/ui/FileInput';
import ImgInput from '@/components/ui/ImgInput';
import { Input } from '@/components/ui/Input';
import { Label } from '@/components/ui/Label';
import { Textarea } from '@/components/ui/Textarea';
import { destroy, index, update } from '@/wayfinder/routes/admin/info-items';
import { Inertia } from '@/wayfinder/types';
import { router, useForm } from '@inertiajs/react';
import { Trash2 } from 'lucide-react';
import { FC, useState } from 'react';
import { toast } from 'sonner';

const Edit: FC<Inertia.Pages.Admin.InfoItems.Edit> = ({ infoItem }) => {
    const { data, setData, post, processing, errors } = useForm({
        _method: 'put',
        title: infoItem.title ?? '',
        url: infoItem.url ?? '',
        image: null as File | null,
        description: infoItem.description ?? '',
        file_url: null,
        file_name: infoItem.file_name ?? '',
    });

    const [isDeleting, setIsDeleting] = useState(false);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(update({ info_item: infoItem.id }).url, {
            onSuccess: () => toast.success('Элемент успешно обновлён'),
        });
    };

    const handleDelete = () => {
        setIsDeleting(true);
        router.delete(destroy({ info_item: infoItem.id }).url, {
            onSuccess: () => toast.success('Элемент успешно удалён'),
            onError: () => {
                toast.error('Ошибка при удалении элемента');
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
                            data-test="delete-info-item"
                        >
                            Удалить элемент
                            <Trash2 />
                        </Button>
                    }
                    title="Удалить элемент?"
                    description={`Вы уверены, что хотите удалить "${infoItem.title}"? Это действие нельзя отменить.`}
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
                    <h2>Редактировать информационный элемент</h2>
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
                            name="title"
                            value={data.title}
                            onChange={(e) => setData('title', e.target.value)}
                        />
                        <InputError message={errors.title} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="url">Ссылка</Label>
                        <Input
                            id="url"
                            type="text"
                            name="url"
                            value={data.url}
                            onChange={(e) => setData('url', e.target.value)}
                        />
                        <InputError message={errors.url} />
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
                        <Label htmlFor="file_url">Прикрепить файл</Label>
                        <FileInput
                            isEdited={true}
                            id="file_url"
                            src={infoItem?.file_url}
                            filename={infoItem?.file_name}
                            error={errors.file_url}
                            onChange={(file) => {
                                setData('file_url', file);
                                if (file) setData('file_name', file.name);
                            }}
                        />
                        <InputError message={errors.file_url} />
                    </div>
                    <div className="grid gap-2">
                        <ImgInput
                            isEdited={true}
                            src={infoItem.image?.webp2x}
                            error={errors.image}
                            label="Изображение"
                            onChange={(file) => setData('image', file)}
                        />
                        <InputError message={errors.image} />
                    </div>
                </div>

                <FormButtons
                    label="Сохранить"
                    processing={processing}
                    backUrl={index().url}
                />
            </form>
        </div>
    );
};

export default Edit;
