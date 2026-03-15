import AccentHeading from '@/components/ui/AccentHeading';
import { Button } from '@/components/ui/Button';
import { SelectMenu } from '@/components/ui/SelectMenu';
import { App } from '@/wayfinder/types';
import { router, usePage } from '@inertiajs/react';
import { toast } from 'sonner';
import { formatExpoValue } from './utils';
import { useState } from 'react';
import DashboardController from '@/wayfinder/App/Http/Controllers/Admin/DashboardController';

const ExpoSelector = () => {
    const { expos, exhibition } = usePage<{
        expos: App.Models.Exhibition[];
        exhibition: App.Models.Exhibition | null;
    }>().props;

    const [selectedId, setSelectedId] = useState<string | null>(exhibition?.id.toString() ?? null);

    const handleClick = () => {
        if (!selectedId) return;

        router.visit(
            DashboardController.update({ dashboard: selectedId }),
            {
                method: 'patch',
                onSuccess: () => {
                    toast('Выставка успешно активирована');
                    router.flush('exhibitions');
                },
                preserveScroll: true,
                preserveState: true,
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
                        Активировать
                    </Button>
                </div>
            )}
        </>
    );
};

export default ExpoSelector;
