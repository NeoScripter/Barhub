import FormButtons from '@/components/form/FormButtons';
import InputError from '@/components/form/InputError';
import AccentHeading from '@/components/ui/AccentHeading';
import { Button } from '@/components/ui/Button';
import { DeleteAlertDialog } from '@/components/ui/DeleteAlertDialog';
import { Input } from '@/components/ui/Input';
import { Label } from '@/components/ui/Label';
import { Textarea } from '@/components/ui/Textarea';
import { update, destroy, index } from '@/wayfinder/App/Http/Controllers/Admin/CompanyFollowupController';
import { Inertia } from '@/wayfinder/types';
import { router, useForm } from '@inertiajs/react';
import { Trash2 } from 'lucide-react';
import { FC, useState } from 'react';
import { toast } from 'sonner';

const Edit: FC<Inertia.Pages.Admin.CompanyFollowups.Edit> = ({
    company,
    followup,
}) => {
    const { data, setData, put, processing, errors } = useForm({
        name: followup.name,
        description: followup.description,
        comment: followup.comment,
    });

    const [isDeleting, setIsDeleting] = useState(false);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        put(update({ company: company.id, followup: followup.id }).url, {
            onSuccess: () => {
                toast.success('Заявка на услугу успешно обновлена');
            },
        });
    };

    const handleDelete = () => {
        setIsDeleting(true);
        router.delete(destroy({ company: company.id, followup }).url, {
            onSuccess: () => {
                router.visit(index({ company: company.id }).url);
                toast.success('Заявка на услугу успешно удалена');
            },
            onError: () => {
                toast.error('Ошибка при удалении заявки на услугу');
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
                            data-test="delete-followup"
                        >
                            Удалить заявку на услугу
                            <Trash2 />
                        </Button>
                    }
                    title="Удалить заявку на услугу?"
                    description={`Вы уверены, что хотите удалить заявку на услугу "${followup.name}"? Это действие нельзя отменить.`}
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
                    <h2>Редактировать заявку на услугу</h2>
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
                            placeholder="Введите название заявки на услугу"
                        />
                        <InputError message={errors.name} />
                    </div>

                    <div className="grid md:col-span-2 gap-2">
                        <Label htmlFor="description">Описание</Label>
                        <Textarea
                            id="description"
                            name="description"
                            value={data.description}
                            onChange={(e) =>
                                setData('description', e.target.value)
                            }
                            className="max-w-full"
                            placeholder="Введите описание заявки на услугу"
                        />
                        <InputError message={errors.description} />
                    </div>

                    <div className="grid md:col-span-2 gap-2">
                        <Label htmlFor="comment">Комментарий</Label>
                        <Textarea
                            id="comment"
                            name="comment"
                            value={data.comment}
                            onChange={(e) =>
                                setData('comment', e.target.value)
                            }
                            className="max-w-full"
                            placeholder="Введите комментарий"
                        />
                        <InputError message={errors.comment} />
                    </div>

                </div>

                <FormButtons
                    label="Сохранить"
                    processing={processing}
                    backUrl={index({ company: company.id }).url}
                />
            </form>
        </div>
    );
};

export default Edit;
