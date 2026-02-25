import { cn } from '@/lib/utils';
import { Check } from 'lucide-react';
import { useState } from 'react';

type MultiSelectProps<T> = {
    items: T[];
    selectedValues: number[];
    onValueChange: (values: number[]) => void;
    getLabel: (item: T) => string;
    getValue: (item: T) => number;
    placeholder?: string;
};

export function MultiSelect<T>({
    items,
    selectedValues,
    onValueChange,
    getLabel,
    getValue,
    placeholder = 'Выберите элементы',
}: MultiSelectProps<T>) {
    const [open, setOpen] = useState(false);

    const toggleItem = (value: number) => {
        const newValues = selectedValues.includes(value)
            ? selectedValues.filter((v) => v !== value)
            : [...selectedValues, value];
        onValueChange(newValues);
    };

    return (
        <div className="relative">
            <button
                type="button"
                onClick={() => setOpen(!open)}
                className="border-input bg-white flex h-10 w-full items-center justify-between rounded-md border px-3 py-2 text-sm"
            >
                <span>
                    {selectedValues.length > 0
                        ? `Выбрано: ${selectedValues.length}`
                        : placeholder}
                </span>
            </button>

            {open && (
                <div className="bg-white absolute z-10 mt-2 max-h-60 w-full overflow-auto rounded-md border p-1 shadow-md">
                    {items.map((item) => {
                        const value = getValue(item);
                        const isSelected = selectedValues.includes(value);
                        return (
                            <div
                                key={value}
                                onClick={() => toggleItem(value)}
                                className={cn(
                                    'flex cursor-pointer items-center rounded-sm px-2 py-1.5 text-sm hover:bg-accent',
                                    isSelected && 'bg-accent',
                                )}
                            >
                                <Check
                                    className={cn(
                                        'mr-2 h-4 w-4',
                                        isSelected
                                            ? 'opacity-100'
                                            : 'opacity-0',
                                    )}
                                />
                                {getLabel(item)}
                            </div>
                        );
                    })}
                </div>
            )}
        </div>
    );
}
