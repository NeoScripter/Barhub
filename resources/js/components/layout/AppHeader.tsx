import { NodeProps } from '@/types/ui';
import { FC } from 'react';

const AppHeader: FC<NodeProps> = ({ children }) => {
    return (
        <header className="">
            This is header
            {children}
        </header>
    );
};

export default AppHeader;
