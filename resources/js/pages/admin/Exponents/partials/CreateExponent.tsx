import { Button } from '@/components/ui/Button';
import { SelectMenu } from '@/components/ui/SelectMenu';
import { update } from '@/wayfinder/routes/admin/exponents';
import { Inertia } from '@/wayfinder/types';
import { router, usePage } from '@inertiajs/react';
import { useState } from 'react';
import { toast } from 'sonner';

const CreateExponent = () => {
    const { users, exhibition, company } =
        usePage<Inertia.Pages.Admin.Exponents.Index>().props;
    const [selectedId, setSelectedId] = useState<string | null>(null);

    const handleClick = () => {
        if (!selectedId) return;

        router.visit(update({ exhibition, company, exponent: selectedId }), {
            method: 'patch',
            onSuccess: () => {
                toast.success('Экспонент успешно создан');
            },
            preserveScroll: true,
        });
    };

    return (
        <div className="flex w-full flex-col items-center gap-4 sm:gap-6 lg:gap-8">
            <SelectMenu
                items={users}
                getValue={(user) => user.id.toString()}
                getLabel={(user) => user.email}
                placeholder="Выбрать пользователя"
                className="max-w-90"
                onValueChange={(value) => setSelectedId(value)}
                variant="default"
            />
            <Button
                onClick={handleClick}
                variant="default"
                size="sm"
                className="mx-auto"
                data-test="add-exponent"
                disabled={selectedId == null}
            >
                Добавить
            </Button>
        </div>
    );
};

export default CreateExponent;
