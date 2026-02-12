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

export function shortenDescription(desc: string, limit = 15) {
    return (
        desc.split(' ').slice(0, limit).join(' ') +
        (desc.split(' ').length > limit ? '...' : '')
    );
}
