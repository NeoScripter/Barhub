import FormButtons from '@/components/form/FormButtons';
import InputError from '@/components/form/InputError';
import AccentHeading from '@/components/ui/AccentHeading';
import { Button } from '@/components/ui/Button';
import { DeleteAlertDialog } from '@/components/ui/DeleteAlertDialog';
import { Input } from '@/components/ui/Input';
import { Label } from '@/components/ui/Label';
import RadioCheckbox from '@/components/ui/RadioCheckbox';
import { convertDateToInputString } from '@/lib/utils';
import { destroy, index, update } from '@/wayfinder/routes/admin/exhibitions';
import { Inertia } from '@/wayfinder/types';
import { router, useForm } from '@inertiajs/react';
import { Trash2 } from 'lucide-react';
import { FC, useState } from 'react';
import { toast } from 'sonner';

const Edit: FC<Inertia.Pages.Admin.Exhibitions.Edit> = ({ exhibition }) => {
    const { data, setData, put, processing, errors } = useForm({
        name: exhibition.name,
        starts_at: convertDateToInputString(exhibition.starts_at),
        ends_at: convertDateToInputString(exhibition.ends_at),
        location: exhibition.location,
        buildin_folder_url: exhibition.buildin_folder_url,
        is_active: !!exhibition.is_active,
    });

    const [isDeleting, setIsDeleting] = useState(false);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        put(update({ exhibition }).url, {
            onSuccess: () => toast.success('Выставка успешно обновлена'),
        });
    };

    const handleDelete = () => {
        setIsDeleting(true);
        router.delete(destroy({ exhibition }).url, {
            onSuccess: () => toast.success('Выставка успешно удалена'),
            onError: () => {
                toast.error('Ошибка при удалении выставки');
                setIsDeleting(false);
            },
        });
    };

    return (
        <div className="mx-auto w-full max-w-250">
            <div className="mb-8 text-center md:mb-12">
                <AccentHeading
                    asChild
                    className="mb-1 text-lg text-secondary"
                >
                    <h2>Редактировать выставку</h2>
                </AccentHeading>
            </div>

            <div className="mb-8 flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
                <DeleteAlertDialog
                    trigger={
                        <Button
                            variant="destructive"
                            type="button"
                            data-test="delete-exhibition"
                        >
                            Удалить выставку
                            <Trash2 />
                        </Button>
                    }
                    title="Удалить выставку?"
                    description={`Вы уверены, что хотите удалить выставку "${exhibition.name}"? Это действие нельзя отменить.`}
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
                        <Label htmlFor="name">Название</Label>
                        <Input
                            id="name"
                            type="text"
                            value={data.name}
                            onChange={(e) => setData('name', e.target.value)}
                        />
                        <InputError message={errors.name} />
                    </div>

                    <div className="grid gap-6">
                        <div className="grid gap-2">
                            <Label htmlFor="starts_at">Дата начала</Label>
                            <Input
                                id="starts_at"
                                type="date"
                                value={data.starts_at}
                                onChange={(e) =>
                                    setData('starts_at', e.target.value)
                                }
                            />
                            <InputError message={errors.starts_at} />
                        </div>

                        <div className="grid gap-2">
                            <Label htmlFor="ends_at">Дата окончания</Label>
                            <Input
                                id="ends_at"
                                type="date"
                                value={data.ends_at}
                                onChange={(e) =>
                                    setData('ends_at', e.target.value)
                                }
                            />
                            <InputError message={errors.ends_at} />
                        </div>
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="location">Место проведения</Label>
                        <Input
                            id="location"
                            type="text"
                            value={data.location}
                            onChange={(e) =>
                                setData('location', e.target.value)
                            }
                        />
                        <InputError message={errors.location} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="buildin_folder_url">
                            Ссылка на информацию и материалы
                        </Label>
                        <Input
                            id="buildin_folder_url"
                            value={data.buildin_folder_url}
                            onChange={(e) =>
                                setData('buildin_folder_url', e.target.value)
                            }
                        />
                        <InputError message={errors.buildin_folder_url} />
                    </div>
                    <div>
                        <RadioCheckbox
                            label="Статус публикации"
                            value={data.is_active}
                            onChange={(val) => setData('is_active', val)}
                        />
                        <InputError message={errors.is_active} />
                    </div>
                </div>

                <FormButtons
                    label="Сохранить"
                    processing={processing}
                    backUrl={index({}).url}
                />
            </form>
        </div>
    );
};

export default Edit;
