import { ChevronDown, LogOut } from 'lucide-react';
import { FC } from 'react';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuPortal,
    DropdownMenuTrigger,
} from '../ui/DropdownMenu';

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

export default AccountDropdown;
