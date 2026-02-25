import InputError from '@/components/form/InputError';
import { Button } from '@/components/ui/Button';
import { Input } from '@/components/ui/Input';
import { Label } from '@/components/ui/Label';
import { SelectMenu } from '@/components/ui/SelectMenu';
import { Spinner } from '@/components/ui/Spinner';
import { Textarea } from '@/components/ui/Textarea';
import { store } from '@/wayfinder/routes/admin/exhibitions/events';
import { Inertia } from '@/wayfinder/types';
import { useForm } from '@inertiajs/react';
import { FC } from 'react';
import { toast } from 'sonner';
import { PersonSelect, PersonWithRoles } from './partials/PersonSelect';
import { ThemeSelect } from './partials/ThemeSelect';
import ThemeDialog from './partials/ThemeDialog';
import StageDialog from './partials/StageDialog';

const Create: FC<Inertia.Pages.Admin.Events.Create> = ({
    exhibition,
    stages,
    themes,
    availablePeople,
    roles,
}) => {
    const { data, setData, post, processing, errors } = useForm({
        title: '',
        description: '',
        stage_id: null as number | null,
        theme_ids: [] as number[],
        people: [] as PersonWithRoles[],
        starts_at: '',
        ends_at: '',
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(store({ exhibition }).url, {
            onSuccess: () => toast.success('Событие успешно создано'),
        });
    };

    return (
        <div className="mx-auto w-full max-w-250">
            <div className="mb-8 flex flex-col gap-8">
                <ThemeDialog />
                <StageDialog />
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
                            placeholder="Введите название события"
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
                            placeholder="Введите описание события"
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
                            value={data.stage_id?.toString() || ''}
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
