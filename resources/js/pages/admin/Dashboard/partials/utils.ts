import { formatDateShort } from '@/lib/utils';
import { App } from '@/wayfinder/types';

export function formatExpoValue(expo: App.Models.Exhibition) {
    return `${formatDateShort(new Date(expo.starts_at))}, ${expo.name}`;
}
