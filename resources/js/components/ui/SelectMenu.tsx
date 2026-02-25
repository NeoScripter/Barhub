import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/Select';
import { cn } from '@/lib/utils';
import { cva, type VariantProps } from 'class-variance-authority';

const selectMenuVariants = cva('bg-white', {
    variants: {
        variant: {
            default:
                'focus-visible:border-muted text-foreground border-foreground focus-visible:ring-foreground/50',
            outline:
                'border-primary text-primary focus-visible:ring-primary/50 ',
            solid: 'border-primary text-white bg-primary focus-visible:ring-primary/50 ',
        },
        size: {
            default: 'pl-5 pr-3 max-w-60 py-2 text-sm gap-2 [&_svg]:size-4 ',
            lg: 'text-base pl-7 max-w-75 pr-5 gap-4 py-3 [&_svg]:size-6 ',
        },
    },
    defaultVariants: {
        variant: 'default',
        size: 'default',
    },
});

type SelectMenuProps<T> = {
    items: T[];
    value?: string;
    defaultValue?: string;
    onValueChange?: (value: string) => void;
    placeholder?: string;
    className?: string;
    label?: string;
    getLabel?: (item: T) => string;
    getValue?: (item: T) => string;
} & VariantProps<typeof selectMenuVariants>;

export function SelectMenu<T = string>({
    items,
    value,
    defaultValue,
    className,
    onValueChange,
    placeholder = 'Выберите элемент',
    label,
    variant,
    size,
    getLabel = (item) => String(item),
    getValue = (item) => String(item),
}: SelectMenuProps<T>) {
    return (
        <Select
            value={value}
            defaultValue={defaultValue}
            onValueChange={onValueChange}
        >
            <SelectTrigger
                className={cn(selectMenuVariants({ variant, size, className }))}
            >
                <SelectValue placeholder={placeholder} />
            </SelectTrigger>
            <SelectContent>
                <SelectGroup>
                    {label && <SelectLabel>{label}</SelectLabel>}
                    {items.map((item) => {
                        const value = getValue(item);
                        return (
                            <SelectItem
                                key={value}
                                value={value}
                            >
                                {getLabel(item)}
                            </SelectItem>
                        );
                    })}
                </SelectGroup>
            </SelectContent>
        </Select>
    );
}
