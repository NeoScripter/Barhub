import type { InertiaLinkProps } from '@inertiajs/react';
import { type ClassValue, clsx } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export function toUrl(url: NonNullable<InertiaLinkProps['href']>): string {
    return typeof url === 'string' ? url : url.url;
}

export function formatDateShort(date: Date, options?: Intl.DateTimeFormatOptions) {
    return new Intl.DateTimeFormat('ru', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    }).format(date);
}
