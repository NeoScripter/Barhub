import Logo from '@/assets/svgs/logo.svg';
import { cn } from '@/lib/utils';
import { NodeProps } from '@/old-types/ui';
import { FC } from 'react';

const AppLogo: FC<NodeProps> = ({ className }) => {
    return (
        <div className={cn('w-37.5 shrink-0 sm:w-42.5 xl:w-64', className)}>
            <img
                src={Logo}
                alt="barhub expo"
                className="size-full object-contain"
            />
        </div>
    );
};

export default AppLogo;
