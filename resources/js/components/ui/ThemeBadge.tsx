import { cn } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import { App } from '@/wayfinder/types';
import { FC } from 'react';

const ThemeBadge: FC<NodeProps<{ theme: App.Models.Theme }>> = ({
    className,
    theme,
}) => {
    return (
        <li
            key={theme.name}
            className={cn('rounded-full px-2 py-1 text-xs', className)}
            style={{ backgroundColor: theme.color_hex }}
        >
            {theme.name}
        </li>
    );
};

export default ThemeBadge;
