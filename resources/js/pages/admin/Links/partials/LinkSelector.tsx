import { Button } from '@/components/ui/Button';
import { SelectMenu } from '@/components/ui/SelectMenu';
import { useClipboard } from '@/hooks/useClipboard';
import { FC, useEffect, useState } from 'react';
import { toast } from 'sonner';

export type LinkOptionType = {
    id: number;
    value: string;
};

const LinkSelector: FC<{
    options: LinkOptionType[];
    setter: (val: number) => string;
    label: string;
}> = ({ options, setter, label }) => {
    const [selected, setSelected] = useState<string | null>(null);
    const [copiedText, copy] = useClipboard();

    const handleClick = () => {
        if (!selected) {
            console.error('No id is selected');
            return;
        }

        copy(window.location.hostname + setter(Number(selected)));

        // TODO: Make proper error handling
        // if (!copiedText) {
        //     toast.error('Ошибка копирования ссылки');
        //     return;
        // }

        toast.success('Ссылка успешно скопирована!');
    };

    return (
        <>
            {options && (
                <div className="flex w-full flex-col items-center gap-4 sm:gap-6 lg:gap-8">
                    <SelectMenu
                        items={options}
                        getValue={(option) => option.id.toString()}
                        getLabel={(option) => option.value}
                        placeholder={`Выбрать ${label}`}
                        className="max-w-70 sm:max-w-90"
                        onValueChange={(option) => setSelected(option)}
                        variant="default"
                    />
                    {selected != null && (
                        <Button
                            onClick={handleClick}
                            variant="default"
                            size="sm"
                            className="mx-auto"
                        >
                            Скопировать ссылку
                        </Button>
                    )}
                </div>
            )}
        </>
    );
};

export default LinkSelector;
