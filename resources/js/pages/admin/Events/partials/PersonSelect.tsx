import InputError from '@/components/form/InputError';
import { Button } from '@/components/ui/Button';
import { Label } from '@/components/ui/Label';
import { SelectMenu } from '@/components/ui/SelectMenu';
import { X } from 'lucide-react';

type Person = {
    id: number;
    name: string;
};

type Role = {
    value: number;
    label: string;
};

export type PersonWithRole = {
    person_id: number;
    role: number;
};

type PersonSelectProps = {
    availablePeople: Person[];
    roles: Role[];
    selectedPeople: PersonWithRole[];
    onChange: (people: PersonWithRole[]) => void;
    errors?: Record<string, string>;
};

export function PersonSelect({
    availablePeople,
    roles,
    selectedPeople,
    onChange,
    errors = {},
}: PersonSelectProps) {
    const addPerson = () => {
        const firstAvailablePerson = getUnselectedPeople().find(Boolean);

        if (!firstAvailablePerson) return;

        onChange([
            ...selectedPeople,
            {
                person_id: firstAvailablePerson.id,
                role: roles[0].value,
            },
        ]);
    };

    const removePerson = (index: number) => {
        onChange(selectedPeople.filter((_, i) => i !== index));
    };

    const updatePerson = (
        index: number,
        field: keyof PersonWithRole,
        value: number,
    ) => {
        const updated = [...selectedPeople];
        updated[index][field] = value;
        onChange(updated);
    };

    const getUnselectedPeople = () => {
        const selectedIds = selectedPeople.map((p) => p.person_id);
        return availablePeople.filter((p) => !selectedIds.includes(p.id));
    };

    const getAvailablePeopleForSelect = (currentPersonId: number) => {
        const otherSelectedIds = selectedPeople
            .map((p) => p.person_id)
            .filter((id) => id !== currentPersonId);
        return availablePeople.filter((p) => !otherSelectedIds.includes(p.id));
    };

    const canAddMore = selectedPeople.length < availablePeople.length;

    return (
        <div className="grid gap-4">
            <Label>Участники</Label>

            {selectedPeople.length > 0 ? (
                <div className="grid gap-4">
                    {selectedPeople.map((person, index) => (
                        <div
                            key={index}
                            className="grid gap-4 rounded border p-4 sm:grid-cols-[1fr,1fr,auto]"
                        >
                            <div className="grid gap-2">
                                <Label className="text-sm font-normal">
                                    Человек
                                </Label>
                                <SelectMenu
                                    items={getAvailablePeopleForSelect(
                                        person.person_id,
                                    )}
                                    value={person.person_id.toString()}
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
                                <Label className="text-sm font-normal">
                                    Роль
                                </Label>
                                <SelectMenu
                                    items={roles}
                                    value={person.role.toString()}
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
                                    size="icon"
                                    onClick={() => removePerson(index)}
                                >
                                    <X className="h-4 w-4" />
                                </Button>
                            </div>
                        </div>
                    ))}
                </div>
            ) : (
                <p className="text-sm text-muted-foreground">
                    Участники не добавлены
                </p>
            )}

            <Button
                type="button"
                variant="outline"
                onClick={addPerson}
                className="w-fit"
                disabled={!canAddMore}
            >
                Добавить участника
            </Button>

            <InputError message={errors.people} />
        </div>
    );
}
