import { cn, getFilterUrl, isActiveFilter } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import { Link } from '@inertiajs/react';
import { LucideIcon } from 'lucide-react';
import { FC } from 'react';

const EventFilter: FC<
    NodeProps<{
        icon: LucideIcon;
        label: string;
        filters: string[];
        filterKey: string;
        modifier?: (val: string) => string;
    }>
> = ({ className, icon, label, filters, filterKey, modifier }) => {
    const Icon = icon;

    return (
        <li
            className={cn(
                'flex flex-col gap-4 sm:flex-row sm:items-start sm:gap-6',
                className,
            )}
        >
            <p className="flex items-center gap-2.5 sm:mt-0.5 2xl:gap-3.5 2xl:text-lg">
                <Icon className="size-4.5 2xl:size-5.5" />
                {label}
            </p>
            <ul className="flex flex-wrap gap-3">
                {filters.map((filter) => (
                    <FilterBtn
                        key={filter}
                        filter={modifier ? modifier(filter) : filter}
                        url={getFilterUrl(filterKey, filter)}
                        isActive={isActiveFilter(filterKey, filter)}
                    />
                ))}
            </ul>
        </li>
    );
};

export default EventFilter;

const FilterBtn: FC<{ filter: string; isActive?: boolean; url: string }> = ({
    filter,
    isActive = false,
    url,
}) => {
    return (
        <li
            className={cn(
                'rounded-full link-hover border border-primary px-3 py-1 text-sm text-primary transition-[colors,opacity] select-none 2xl:text-base',
                isActive && 'bg-primary text-white',
            )}
        >
            <Link
                href={url}
                preserveScroll
                preserveState
            >
                {filter}
            </Link>
        </li>
    );
};
