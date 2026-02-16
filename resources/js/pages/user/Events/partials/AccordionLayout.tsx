import { cn } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import { FC } from 'react';

const AccordionLayout: FC<NodeProps> = ({ children, className }) => {
    return (
        <div
            className={cn(
                'top-0 z-20 bg-white opacity-0 transition-opacity duration-50 ease-in-out group-hover:z-20 group-hover:opacity-100 md:group-hover:absolute group-hover:relative absolute',
            )}
        >
            <ul className={cn("[&>*:max-h-0] [&>*:transition-[max-height]] [&>*:duration-150] [&>*:ease-in-out] [&>*:group-hover:max-h-100] grid gap-2 rounded-sm bg-white transition-[padding] duration-150 ease-in-out sm:mt-2 md:mt-0 md:shadow-sm md:group-hover:p-1", className)}>
                {children}
            </ul>
        </div>
    );
};

export default AccordionLayout;
