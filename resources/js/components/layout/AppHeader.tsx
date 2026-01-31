import { cn } from '@/lib/utils';
import { NodeProps } from '@/types/ui';
import { FC } from 'react';
import AccentHeading from '../ui/AccentHeading';
import BurgerMenuIcon from '../ui/BurgerMenuIcon';
import { Dialog, DialogContent, DialogTrigger } from '../ui/Dialog';
import AccountDropdown from './AccountDropdown';
import AppLogo from './AppLogo';
import NavMenu from './NavMenu';

type Props = NodeProps & {
    variant?: 'user' | 'admin';
};

const AppHeader: FC<Props> = ({ variant = 'admin' }) => {
    const isAdmin = variant === 'admin';

    return (
        <header
            className={cn(
                'flex items-center justify-between gap-2 py-6.5 sm:py-9.5 xl:py-11',
                { 'sm:items-baseline': !isAdmin },
            )}
        >
            <AppLogo />
            <AccentHeading
                asChild
                className={cn({
                    'hidden lg:block': isAdmin,
                    'text-right': !isAdmin,
                })}
            >
                <h1>Название выставки</h1>
            </AccentHeading>

            {isAdmin && <AccountMenuControls />}
        </header>
    );
};

export default AppHeader;

const AccountMenuControls = () => {
    return (
        <>
            <Dialog>
                <DialogTrigger asChild>
                    <button className="mr-2 size-8 sm:size-9 lg:hidden">
                        <BurgerMenuIcon className="size-full" />
                    </button>
                </DialogTrigger>
                <DialogContent className="top-0 right-0 left-auto h-full translate-0 sm:h-max sm:rounded-bl-3xl lg:hidden">
                    <NavMenu />
                </DialogContent>
            </Dialog>
            <AccountDropdown
                className="hidden lg:block"
                email="admin@gmail.com"
            />
        </>
    );
};
