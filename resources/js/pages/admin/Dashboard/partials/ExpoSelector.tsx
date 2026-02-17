import AccentHeading from '@/components/ui/AccentHeading';
import { Button } from '@/components/ui/Button';
import { SelectMenu } from '@/components/ui/SelectMenu';
import UpdatedExhibitionStatusController from '@/wayfinder/App/Http/Controllers/Admin/UpdatedExhibitionStatusController';
import { App } from '@/wayfinder/types';
import { router, usePage } from '@inertiajs/react';
import { useState } from 'react';
import { toast } from 'sonner';
import { formatExpoValue } from './utils';

const ExpoSelector = () => {
    const { expos } = usePage<{ expos: App.Models.Exhibition[] }>().props;
    const [selectedId, setSelectedId] = useState<string | null>(null);

    const selectedExpo = expos.find((expo) => expo.id === Number(selectedId));

    const handleClick = () => {
        if (!selectedExpo) return;

        router.visit(
            UpdatedExhibitionStatusController.patch({ id: selectedExpo.id }),
            {
                method: 'patch',
                onSuccess: () => {
                    const action = selectedExpo.is_active
                        ? 'деактивирована'
                        : 'активирована';
                    toast(`Выставка успешно ${action}`);
                    router.flush('exhibitions')
                },
                preserveScroll: true,
                preserveState: true,
                data: {
                    is_active: !selectedExpo.is_active,
                },
            },
        );
    };

    return (
        <>
            <AccentHeading className="text-center text-sm">
                Название выставки
            </AccentHeading>

            {expos && (
                <div className="flex w-full flex-col items-center gap-4 sm:gap-6 lg:gap-8">
                    <SelectMenu
                        items={expos}
                        getValue={(expo) => expo.id.toString()}
                        getLabel={(expo) => formatExpoValue(expo)}
                        placeholder="Выбрать выставку"
                        className="max-w-90"
                        onValueChange={(value) => setSelectedId(value)}
                        variant="default"
                    />
                    <Button
                        onClick={handleClick}
                        variant="default"
                        size="sm"
                        className="mx-auto"
                        disabled={selectedId == null}
                    >
                        {selectedExpo?.is_active
                            ? 'Деактивировать'
                            : 'Активировать'}
                    </Button>
                </div>
            )}
        </>
    );
};

export default ExpoSelector;
