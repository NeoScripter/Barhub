import { cn } from '@/lib/utils';
import { NodeProps } from '@/types/ui';
import { ChevronDown, LogOut } from 'lucide-react';
import { FC } from 'react';
import AccentHeading from '../ui/AccentHeading';
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
        <header className="flex items-baseline justify-between gap-2 py-6.5 sm:py-9.5 xl:py-11">
            <AppLogo />
            <AccentHeading
                asChild
                className={cn({ 'hidden lg:block': isAdmin })}
            >
                <h1>Название выставки</h1>
            </AccentHeading>
            {isAdmin && <AccountMenu email="admin@gmail.com" />}
        </header>
    );
};

export default AppHeader;

const AccountMenu: FC<{ email: string }> = ({ email }) => {
    return (
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
    );
};
