import FormButtons from '@/components/form/FormButtons';
import InputError from '@/components/form/InputError';
import AccentHeading from '@/components/ui/AccentHeading';
import { Button } from '@/components/ui/Button';
import { DeleteAlertDialog } from '@/components/ui/DeleteAlertDialog';
import { Input } from '@/components/ui/Input';
import { Label } from '@/components/ui/Label';
import RadioCheckbox from '@/components/ui/RadioCheckbox';
import { Textarea } from '@/components/ui/Textarea';
import { destroy, index, update } from '@/wayfinder/routes/admin/services';
import { Inertia } from '@/wayfinder/types';
import { router, useForm } from '@inertiajs/react';
import { Trash2 } from 'lucide-react';
import { FC, useState } from 'react';
import { toast } from 'sonner';

const Edit: FC<Inertia.Pages.Admin.Services.Edit> = ({ service }) => {
    const { data, setData, put, processing, errors } = useForm({
        name: service.name,
        description: service.description,
        is_active: !!service.is_active,
    });

    const [isDeleting, setIsDeleting] = useState(false);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        put(update({ service: service.id }).url, {
            onSuccess: () => {
                toast.success('Услуга успешно обновлена');
            },
        });
    };

    const handleDelete = () => {
        setIsDeleting(true);
        router.delete(destroy({ service }).url, {
            onSuccess: () => {
                router.visit(index().url);
                toast.success('Услуга успешно удалена');
            },
            onError: () => {
                toast.error('Ошибка при удалении услуги');
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
                            data-test="delete-service"
                        >
                            Удалить услугу
                            <Trash2 />
                        </Button>
                    }
                    title="Удалить услугу?"
                    description={`Вы уверены, что хотите удалить услугу "${service.name}"? Это действие нельзя отменить.`}
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
                    <h2>Редактировать услугу</h2>
                </AccentHeading>
            </div>
            <form
                onSubmit={handleSubmit}
                className="flex flex-col gap-6"
            >
                <div className="grid gap-6">
                    <div className="grid gap-2 md:col-span-2">
                        <Label htmlFor="name">Название</Label>
                        <Input
                            id="name"
                            type="text"
                            name="name"
                            value={data.name}
                            onChange={(e) => setData('name', e.target.value)}
                            placeholder="Введите название услуги"
                        />
                        <InputError message={errors.name} />
                    </div>

                    <div className="grid gap-2 md:col-span-2">
                        <Label htmlFor="description">Описание</Label>
                        <Textarea
                            id="description"
                            name="description"
                            value={data.description}
                            onChange={(e) =>
                                setData('description', e.target.value)
                            }
                            className="max-w-full"
                            placeholder="Введите описание услуги"
                        />
                        <InputError message={errors.description} />
                    </div>

                    <div className="md:col-span-2">
                        <RadioCheckbox
                            label='Статус услуги'
                            value={data.is_active}
                            onChange={(val) => setData('is_active', val)}
                        />
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
