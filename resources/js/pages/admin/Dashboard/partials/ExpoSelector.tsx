import AccentHeading from '@/components/ui/AccentHeading';
import { Button } from '@/components/ui/Button';
import { SelectMenu } from '@/components/ui/SelectMenu';
import { App } from '@/wayfinder/types';
import { router, usePage } from '@inertiajs/react';
import { FC } from 'react';
import { toast } from 'sonner';
import { formatExpoValue } from './utils';
import ExhibitionUpdatedStatusController from '@/wayfinder/App/Http/Controllers/Admin/ExhibitionUpdatedStatusController';

const ExpoSelector: FC<{
    expoId: string | null;
    setter: (val: string) => void;
}> = ({ expoId, setter }) => {
    const { expos } = usePage<{ expos: App.Models.Exhibition[] }>().props;

    const selectedExpo = expos.find((expo) => expo.id === Number(expoId));

    const handleClick = () => {
        if (!selectedExpo) return;

        router.visit(
            ExhibitionUpdatedStatusController.patch({ id: selectedExpo.id }),
            {
                method: 'patch',
                onSuccess: () => {
                    const action = selectedExpo.is_active
                        ? 'деактивирована'
                        : 'активирована';
                    toast(`Выставка успешно ${action}`);
                    router.flush('exhibitions');
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
                        className="max-w-70 sm:max-w-90"
                        onValueChange={(value) => setter(value)}
                        variant="default"
                    />
                    <Button
                        onClick={handleClick}
                        variant="default"
                        size="sm"
                        className="mx-auto"
                        disabled={expoId == null}
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
