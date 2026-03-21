import { cn } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import { FC } from 'react';

const InfoItem: FC<NodeProps> = ({ className, children }) => {
    return (
        <li
            className={cn(
                'flex items-center gap-3 sm:gap-4 xl:text-xl text-sm sm:text-base font-bold [&>svg]:size-5 [&>svg]:sm:size-6 [&>svg]:xl:size-7 [&>svg]:text-primary',
                className,
            )}
        >
            {' '}
            {children}
        </li>
    );
};

export default InfoItem;
