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

export type PersonWithRoles = {
    person_id: number;
    roles: number[];
};

type PersonSelectProps = {
    availablePeople: Person[];
    roles: Role[];
    selectedPeople: PersonWithRoles[];
    onChange: (people: PersonWithRoles[]) => void;
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
                roles: [roles[0].value],
            },
        ]);
    };

    const removePerson = (index: number) => {
        onChange(selectedPeople.filter((_, i) => i !== index));
    };

    const updatePersonId = (index: number, personId: number) => {
        const updated = [...selectedPeople];
        updated[index].person_id = personId;
        onChange(updated);
    };

    const addRoleToPerson = (index: number, roleValue: string) => {
        const role = parseInt(roleValue);
        const updated = [...selectedPeople];

        if (!updated[index].roles.includes(role)) {
            updated[index].roles = [...updated[index].roles, role];
            onChange(updated);
        }
    };

    const removeRoleFromPerson = (personIndex: number, role: number) => {
        const updated = [...selectedPeople];
        updated[personIndex].roles = updated[personIndex].roles.filter(
            (r) => r !== role,
        );
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

    const getAvailableRolesForPerson = (personIndex: number) => {
        const selectedRoles = selectedPeople[personIndex].roles;
        return roles.filter((role) => !selectedRoles.includes(role.value));
    };

    const canAddMore = selectedPeople.length < availablePeople.length;

    return (
        <div className="grid gap-2">
            <Label>Участники</Label>

            {selectedPeople.length > 0 ? (
                <div className="grid max-w-max gap-4">
                    {selectedPeople.map((person, personIndex) => (
                        <div
                            key={personIndex}
                            className="flex flex-col gap-4 rounded border border-muted p-4 sm:gap-6"
                        >
                            <div className="grid gap-1">
                                <Label className="text-sm">Участник</Label>
                                <SelectMenu
                                    items={getAvailablePeopleForSelect(
                                        person.person_id,
                                    )}
                                    value={person.person_id.toString()}
                                    onValueChange={(value) =>
                                        updatePersonId(
                                            personIndex,
                                            parseInt(value),
                                        )
                                    }
                                    getLabel={(p) => p.name}
                                    getValue={(p) => p.id.toString()}
                                    placeholder="Выберите участника"
                                    className="w-120 rounded-md"
                                />
                                <InputError
                                    message={
                                        errors[
                                            `people.${personIndex}.person_id`
                                        ]
                                    }
                                />
                            </div>

                            <div className="grid gap-3">
                                <Label className="text-sm">Роли</Label>
                                {getAvailableRolesForPerson(personIndex)
                                    .length > 0 && (
                                    <SelectMenu
                                        items={getAvailableRolesForPerson(
                                            personIndex,
                                        )}
                                        value=""
                                        onValueChange={(value) =>
                                            addRoleToPerson(personIndex, value)
                                        }
                                        getLabel={(r) => r.label}
                                        getValue={(r) => r.value.toString()}
                                        placeholder="Добавить роль"
                                        className="rounded-md"
                                    />
                                )}

                                <div className="flex flex-col gap-2">
                                    {/* Selected roles */}
                                    {person.roles.length > 0 && (
                                        <div className="flex flex-wrap gap-2">
                                            {person.roles.map((role) => {
                                                const roleData = roles.find(
                                                    (r) => r.value === role,
                                                );
                                                return (
                                                    <div
                                                        key={role}
                                                        className="flex items-center gap-2 rounded-md bg-gray-400 px-3 py-1 text-sm text-white"
                                                    >
                                                        <span>
                                                            {roleData?.label}
                                                        </span>
                                                        <button
                                                            type="button"
                                                            onClick={() =>
                                                                removeRoleFromPerson(
                                                                    personIndex,
                                                                    role,
                                                                )
                                                            }
                                                            className="hover:opacity-70"
                                                        >
                                                            <X className="size-4" />
                                                        </button>
                                                    </div>
                                                );
                                            })}
                                        </div>
                                    )}
                                </div>
                                <InputError
                                    message={
                                        errors[`people.${personIndex}.roles`]
                                    }
                                />
                            </div>

                            <div className="flex items-end">
                                <Button
                                    type="button"
                                    variant="destructive"
                                    size="sm"
                                    onClick={() => removePerson(personIndex)}
                                >
                                    Удалить
                                    <X className="size-4" />
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
                variant="secondary"
                onClick={addPerson}
                className="w-fit mt-3"
                disabled={!canAddMore}
            >
                Добавить участника
            </Button>

            <InputError message={errors.people} />
        </div>
    );
}
