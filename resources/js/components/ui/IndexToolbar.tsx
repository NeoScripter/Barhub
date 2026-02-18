import { cn } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import { FC } from 'react';

const IndexToolbar: FC<NodeProps> = ({ className, children }) => {
    return (
        <div
            className={cn(
                'flex flex-col flex-wrap items-start md:items-center justify-between gap-5 py-6.5 sm:py-8 md:flex-row xl:py-9.5',
                className,
            )}
        >
            {children}
        </div>
    );
};

export default IndexToolbar;
