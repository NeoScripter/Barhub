import { cn } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import { FC } from 'react';

const InfoItem: FC<NodeProps> = ({ className, children }) => {
    return (
        <li
            className={cn(
                'flex items-center gap-3 text-sm font-bold sm:gap-4 sm:text-base xl:gap-5 xl:text-lg [&>svg]:size-5 [&>svg]:text-primary [&>svg]:sm:size-6 [&>svg]:xl:size-7',
                className,
            )}
        >
            {' '}
            {children}
        </li>
    );
};

export default InfoItem;
