import AccentHeading from '@/components/ui/AccentHeading';
import { Button } from '@/components/ui/Button';
import { SelectMenu } from '@/components/ui/SelectMenu';
import { App } from '@/wayfinder/types';
import { usePage } from '@inertiajs/react';
import { useState } from 'react';
import { formatExpoValue } from './utils';

const ExpoSelector = () => {
    const { expos } = usePage<{ expos: App.Models.Exhibition[] }>().props;
    const [selectedId, setSelectedId] = useState<string | null>(null);

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
