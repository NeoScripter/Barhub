import type { ReactNode } from 'react';

export type NodeProps<P = {}> = P & {
    children?: ReactNode | string;
    className?: string;
};

export type ImageType = {
    id: number;
    webp3x: string;
    webp2x: string;
    webp: string;
    avif3x: string;
    avif2x: string;
    avif: string;
    tiny: string;
    alt?: string;
};
