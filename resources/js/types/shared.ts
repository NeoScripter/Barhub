import type { ReactNode } from 'react';

export type NodeProps<P = {}> = P & {
    children?: ReactNode | string;
    className?: string;
};
