import InputError from '@/components/form/InputError';
import { Button } from '@/components/ui/Button';
import { DeleteAlertDialog } from '@/components/ui/DeleteAlertDialog';
import { Input } from '@/components/ui/Input';
import { Label } from '@/components/ui/Label';
import { SelectMenu } from '@/components/ui/SelectMenu';
import { Spinner } from '@/components/ui/Spinner';
import { Textarea } from '@/components/ui/Textarea';
import { convertDateToInputString } from '@/lib/utils';
import { destroy, update } from '@/wayfinder/routes/admin/exhibitions/events';
import { Inertia } from '@/wayfinder/types';
import { router, useForm } from '@inertiajs/react';
import { Link, Trash2 } from 'lucide-react';
import { FC, useState } from 'react';
import { toast } from 'sonner';
import { PersonSelect, PersonWithRoles } from './partials/PersonSelect';
import ThemeDialog from './partials/ThemeDialog';
import { ThemeSelect } from './partials/ThemeSelect';

const Edit: FC<Inertia.Pages.Admin.Events.Edit> = ({
    event,
    exhibition,
    eventPeople,
    stages,
    themes,
    availablePeople,
    roles,
}) => {
    const { data, setData, put, processing, errors } = useForm({
        title: event.title,
        description: event.description,
        stage_id: event.stage_id,
        theme_ids: event?.themes?.map((t) => t.id) || [],
        people: (eventPeople as PersonWithRoles[]) || [],
        starts_at: convertDateToInputString(event.starts_at),
        ends_at: convertDateToInputString(event.ends_at),
    });

    const [isDeleting, setIsDeleting] = useState(false);
    const [isEditingTheme, setIsEditingTheme] = useState(false);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        put(update({ event, exhibition }).url, {
            onSuccess: () => toast.success('Событие успешно обновлено'),
        });
    };

    const handleDelete = () => {
        setIsDeleting(true);
        router.delete(destroy({ event, exhibition }).url, {
            onSuccess: () => {
                toast.success('Событие успешно удалено');
            },
            onError: () => {
                toast.error('Ошибка при удалении события');
                setIsDeleting(false);
            },
        });
    };

    const handleCopyLink = () => {
        const eventUrl = `${window.location.origin}/exhibitions/${exhibition.slug}/events/${event.id}`;
        navigator.clipboard.writeText(eventUrl);
        toast.success('Ссылка скопирована');
    };

    return (
        <div className="mx-auto w-full max-w-250">
            <div className="mb-8 flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
                <Button
                    variant="muted"
                    onClick={handleCopyLink}
                    type="button"
                >
                    Копировать ссылку
                    <Link />
                </Button>

                <DeleteAlertDialog
                    trigger={
                        <Button
                            variant="destructive"
                            type="button"
                        >
                            Удалить мероприятие
                            <Trash2 />
                        </Button>
                    }
                    title="Удалить мероприятие?"
                    description={`Вы уверены, что хотите удалить мероприятие "${event.title}"? Это действие нельзя отменить.`}
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
                </div>

                <div className="grid gap-6 sm:grid-cols-2">
                    <div className="grid gap-2">
                        <Label htmlFor="starts_at">Время начала</Label>
                        <Input
                            id="starts_at"
                            type="datetime-local"
                            required
                            value={data.starts_at}
                            onChange={(e) =>
                                setData('starts_at', e.target.value)
                            }
                        />
                        <InputError message={errors.starts_at} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="ends_at">Время окончания</Label>
                        <Input
                            id="ends_at"
                            type="datetime-local"
                            required
                            value={data.ends_at}
                            onChange={(e) => setData('ends_at', e.target.value)}
                        />
                        <InputError message={errors.ends_at} />
                    </div>
                </div>

                <div className="grid gap-6">
                    <div className="grid gap-2">
                        <Label htmlFor="stage_id">Площадка</Label>
                        <SelectMenu
                            items={stages}
                            defaultValue={data.stage_id?.toString()}
                            onValueChange={(value) =>
                                setData('stage_id', parseInt(value))
                            }
                            getLabel={(stage) => stage.name}
                            getValue={(stage) => stage.id.toString()}
                            placeholder="Выберите площадку"
                            className="rounded-md"
                        />
                        <InputError message={errors.stage_id} />
                    </div>

                    <div className="flex flex-wrap items-center justify-between gap-4">
                        <div className="grid max-w-lg gap-2">
                            <Label htmlFor="themes">Направления</Label>
                            <ThemeSelect
                                availableThemes={themes}
                                selectedThemeIds={data.theme_ids}
                                onChange={(themeIds) =>
                                    setData('theme_ids', themeIds)
                                }
                            />
                            <InputError message={errors.theme_ids} />
                        </div>

                        <ThemeDialog
                            isOpen={isEditingTheme}
                            setIsOpen={setIsEditingTheme}
                        />
                    </div>
                </div>

                <PersonSelect
                    availablePeople={availablePeople}
                    roles={roles}
                    selectedPeople={data.people}
                    onChange={(people) => setData('people', people)}
                    errors={errors}
                />

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
