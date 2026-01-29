import { cn } from '@/lib/utils';
import { NodeProps } from '@/types/ui';
import { ChevronDown, LogOut } from 'lucide-react';
import { FC, useState } from 'react';
import AccentHeading from '../ui/AccentHeading';
import BurgerMenu from '../ui/BurgerMenu';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuPortal,
    DropdownMenuTrigger,
} from '../ui/DropdownMenu';
import AppLogo from './AppLogo';

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

const AccountDropdown: FC<{ email: string; className?: string }> = ({
    email,
    className,
}) => {
    return (
        <div className={className}>
            <DropdownMenu>
                <DropdownMenuTrigger className="flex cursor-pointer items-center gap-[0.5em] font-medium text-primary xl:text-xl">
                    {email}
                    <ChevronDown
                        className="w-[1em]"
                        strokeWidth={3}
                    />
                </DropdownMenuTrigger>

                <DropdownMenuPortal>
                    <DropdownMenuContent
                        align="end"
                        side="bottom"
                    >
                        <DropdownMenuItem>
                            <LogOut />
                            Выйти
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenuPortal>
            </DropdownMenu>
        </div>
    );
};

const AccountMenuControls = () => {
    const [show, setShow] = useState(false);
    return (
        <>
            <BurgerMenu
                show={show}
                onClick={() => setShow((prev) => !prev)}
                className="z-5 mr-2 size-8 sm:size-9 lg:hidden"
            />

            <AccountDropdown
                className="hidden lg:block"
                email="admin@gmail.com"
            />
        </>
    );
};
