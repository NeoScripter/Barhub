import InputError from '@/components/form/InputError';
import { Button } from '@/components/ui/Button';
import { Input } from '@/components/ui/Input';
import { Label } from '@/components/ui/Label';
import { MultiSelect } from '@/components/ui/MultiSelect';
import { SelectMenu } from '@/components/ui/SelectMenu';
import { Spinner } from '@/components/ui/Spinner';
import { Textarea } from '@/components/ui/Textarea';
import { convertDateToInputString } from '@/lib/utils';
import { update } from '@/wayfinder/routes/admin/exhibitions/events';
import { Inertia } from '@/wayfinder/types';
import { useForm } from '@inertiajs/react';
import { FC } from 'react';
import { ThemeSelect } from './partials/ThemeSelect';

type PersonWithRole = {
    person_id: number;
    role: number;
};

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
        theme_ids: event?.themes?.map((t) => t.id),
        people: eventPeople as PersonWithRole[],
        starts_at: convertDateToInputString(event.starts_at),
        ends_at: convertDateToInputString(event.ends_at),
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        put(update({ event, exhibition }).url);
    };

    const addPerson = () => {
        setData('people', [
            ...data.people,
            { person_id: 0, role: roles[0].value },
        ]);
    };

    const removePerson = (index: number) => {
        setData(
            'people',
            data.people.filter((_, i) => i !== index),
        );
    };

    const updatePerson = (
        index: number,
        field: keyof PersonWithRole,
        value: number,
    ) => {
        const newPeople = [...data.people];
        newPeople[index][field] = value;
        setData('people', newPeople);
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

                    <div className="grid gap-2 max-w-lg">
                        <Label htmlFor="themes">Темы</Label>
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

                <div className="grid gap-4">
                    <Label>Участники</Label>
                    {data.people.map((person, index) => (
                        <div
                            key={index}
                            className="grid gap-4 rounded border p-4 sm:grid-cols-[1fr,1fr,auto]"
                        >
                            <div className="grid gap-2">
                                <Label>Человек</Label>
                                <SelectMenu
                                    items={availablePeople}
                                    defaultValue={person.person_id.toString()}
                                    onValueChange={(value) =>
                                        updatePerson(
                                            index,
                                            'person_id',
                                            parseInt(value),
                                        )
                                    }
                                    getLabel={(p) => p.name}
                                    getValue={(p) => p.id.toString()}
                                    placeholder="Выберите участника"
                                />
                                <InputError
                                    message={
                                        errors[`people.${index}.person_id`]
                                    }
                                />
                            </div>

                            <div className="grid gap-2">
                                <Label>Роль</Label>
                                <SelectMenu
                                    items={roles}
                                    defaultValue={person.role.toString()}
                                    onValueChange={(value) =>
                                        updatePerson(
                                            index,
                                            'role',
                                            parseInt(value),
                                        )
                                    }
                                    getLabel={(r) => r.label}
                                    getValue={(r) => r.value.toString()}
                                />
                                <InputError
                                    message={errors[`people.${index}.role`]}
                                />
                            </div>

                            <div className="flex items-end">
                                <Button
                                    type="button"
                                    variant="outline"
                                    onClick={() => removePerson(index)}
                                >
                                    Удалить
                                </Button>
                            </div>
                        </div>
                    ))}

                    <Button
                        type="button"
                        variant="outline"
                        onClick={addPerson}
                        className="w-fit"
                    >
                        Добавить участника
                    </Button>
                    <InputError message={errors.people} />
                </div>

                <Button
                    type="submit"
                    className="mt-2 w-fit"
                    disabled={processing}
                >
                    {processing && <Spinner />}
                    Сохранить
                </Button>
            </form>
        </div>
    );
};

export default Edit;
