import { cn } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import { FC } from 'react';

const GridLayout: FC<NodeProps> = ({ className, children }) => {
    return (
        <ul
            className={cn(
                'grid grid-cols-[repeat(auto-fit,minmax(17rem,1fr))] justify-between gap-6 sm:gap-8 [&>li>*]:mx-auto [&>li>*]:w-full [&>li>*]:max-w-[20rem] sm:[&>li>*]:mx-0 sm:[&>li>*]:max-w-full',
                className,
            )}
        >
            {children}
        </ul>
    );
};

export default GridLayout;
