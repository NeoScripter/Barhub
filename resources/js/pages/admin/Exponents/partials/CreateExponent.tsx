import { Button } from '@/components/ui/Button';
import { SelectMenu } from '@/components/ui/SelectMenu';
import { Inertia } from '@/wayfinder/types';
import { usePage } from '@inertiajs/react';
import { useState } from 'react';

const CreateExponent = () => {
    const { users } = usePage<Inertia.Pages.Admin.Exponents.Index>().props;
    const [selectedId, setSelectedId] = useState<string | null>(null);

    const selectedExpo = users.find((user) => user.id === Number(selectedId));

    return (
        <div className="flex w-full flex-col items-center gap-4 sm:gap-6 lg:gap-8">
            <SelectMenu
                items={users}
                getValue={(user) => user.id.toString()}
                getLabel={(user) => user.email}
                placeholder="Выбрать выставку"
                className="max-w-90"
                onValueChange={(value) => setSelectedId(value)}
                variant="default"
            />
            <Button
                variant="default"
                size="sm"
                className="mx-auto"
                disabled={selectedId == null}
            >
                Добавить
            </Button>
        </div>
    );
};

export default CreateExponent;
