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

export function formatDateAndTime(date: Date) {
    return new Intl.DateTimeFormat('ru', {
        hour: '2-digit',
        minute: '2-digit',
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        timeZone: 'UTC',
        hour12: false,
    }).format(date);
}

export function convertDateToInputString(date: string | null) {
    return date ? new Date(date).toISOString().slice(0, 16) : '';
}

export const getSortUrl = (field: string): string => {
    const { pathname, search } = window.location;
    const params = new URLSearchParams(search);

    const current = params.get('sort');
    const isDesc = current === `-${field}`;
    const next = isDesc ? field : `-${field}`;

    params.set('sort', next);
    params.set('page', '1');

    const qs = params.toString();
    return qs ? `${pathname}?${qs}` : pathname;
};

export const getSearchUrl = (query: string): string => {
    const { pathname, search } = window.location;
    const params = new URLSearchParams(search);

    if (query.length === 0) {
        params.delete('search');
    } else {
        params.set('search', query);
        params.set('page', '1');
    }

    const qs = params.toString();
    return qs ? `${pathname}?${qs}` : pathname;
};

export const getFilterUrl = (key: string, value: string): string => {
    const { pathname, search } = window.location;
    const params = new URLSearchParams(search);

    const paramKey = `filter[${key}]`;

    const values = new Set(
        params.get(paramKey)?.split(',').filter(Boolean) ?? [],
    );

    if (values.has(value)) {
        values.delete(value);
    } else {
        values.add(value);
    }

    if (values.size === 0) {
        params.delete(paramKey);
    } else {
        params.set(paramKey, [...values].join(','));
    }

    params.set('page', '1');

    const qs = params.toString();
    return qs ? `${pathname}?${qs}` : pathname;
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
