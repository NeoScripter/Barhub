import { cn } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import { LucideIcon } from 'lucide-react';
import { FC } from 'react';

const EventFilter: FC<
    NodeProps<{ icon: LucideIcon; label: string; filters: string[] }>
> = ({ className, icon, label, filters }) => {
    const Icon = icon;
    return (
        <li
            className={cn(
                'flex flex-col gap-6 sm:flex-row sm:items-start',
                className,
            )}
        >
            <p className="flex items-center gap-2.5 sm:mt-0.5 md:gap-3.5 md:text-lg">
                <Icon className="size-4.5 md:size-5.5" />
                {label}
            </p>
            <ul className="flex flex-wrap gap-3">
                {filters.map((filter) => (
                    <FilterBtn
                        key={filter}
                        filter={filter}
                    />
                ))}
            </ul>
        </li>
    );
};

export default EventFilter;

const FilterBtn: FC<{ filter: string; isActive?: boolean }> = ({
    filter,
    isActive = false,
}) => {
    return (
        <li
            className={cn(
                'rounded-full border border-primary px-3 py-1 text-primary transition-colors',
                isActive && 'bg-primary text-white',
            )}
        >
            {filter}
        </li>
    );
};
