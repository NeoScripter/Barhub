import { cn } from '@/lib/utils';
import { NodeProps } from '@/types/ui';
import { FC } from 'react';
import AccentHeading from '../ui/AccentHeading';
import AccountDropdown from './AccountDropdown';

const NavMenu: FC<NodeProps> = ({ className }) => {
    return (
        <div className={cn('px-7.5 sm:px-19 sm:pb-17', className)}>
            <Header />

            <nav>
                <ul className="grid gap-8"></ul>
            </nav>
        </div>
    );
};

export default NavMenu;

const Header = () => {
    return (
        <header>
            {' '}
            <AccountDropdown
                className="mb-8"
                email="admin@gmail.com"
            />
            <AccentHeading
                asChild
                className="mb-6 text-xl"
            >
                <p>Название выставки</p>
            </AccentHeading>
        </header>
    );
};
