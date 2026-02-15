import type { InertiaLinkProps } from '@inertiajs/react';
import { type ClassValue, clsx } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export function toUrl(url: NonNullable<InertiaLinkProps['href']>): string {
    return typeof url === 'string' ? url : url.url;
}

export function formatDateShort(
    date: Date,
    options?: Intl.DateTimeFormatOptions,
) {
    return new Intl.DateTimeFormat('ru', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        timeZone: 'UTC',
    }).format(date);
}

export function formatTime(date: Date) {
    return new Intl.DateTimeFormat('ru', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: false,
    }).format(date);
}

export const getSortUrl = (query: string): string => {
    const params = new URLSearchParams(window.location.search);
    const currentSort = params.get('sort');

    // Determine new sort direction
    const isCurrentlyDesc = currentSort === `-${query}`;
    const newSort = isCurrentlyDesc ? query : `-${query}`;

    params.set('sort', newSort);

    params.set('page', '1');

    return window.location.pathname + '?' + params.toString();
};

export const getFilterUrl = (key: string, value: string): string => {
    const params = new URLSearchParams(window.location.search);
    const filterKey = `filter[${key}]`;
    const currentFilters = params.get(filterKey)?.split(',') || [];

    let newFilters: string[];

    if (currentFilters.includes(value)) {
        // Remove the value from filters
        newFilters = currentFilters.filter(f => f !== value);

        if (newFilters.length === 0) {
            // If no filters left, remove the param entirely
            params.delete(filterKey);
        } else {
            // Update with remaining filters
            params.set(filterKey, newFilters.join(','));
        }
    } else {
        // Add the value to filters
        newFilters = [...currentFilters, value];
        params.set(filterKey, newFilters.join(','));
    }

    return window.location.pathname + '?' + params.toString();
};

export const isActiveFilter = (key: string, value: string): boolean => {
    const params = new URLSearchParams(window.location.search);
    const filterKey = `filter[${key}]`;
    const currentFilters = params.get(filterKey)?.split(',') || [];

    return currentFilters.includes(value);
};

export function shortenDescription(desc: string, limit = 15) {
    return (
        desc.split(' ').slice(0, limit).join(' ') +
        (desc.split(' ').length > limit ? '...' : '')
    );
}
