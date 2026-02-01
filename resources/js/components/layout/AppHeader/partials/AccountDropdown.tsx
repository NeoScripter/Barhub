import { ChevronDown, LogOut } from 'lucide-react';
import { FC } from 'react';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/DropdownMenu';

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

                <DropdownMenuContent
                    align="end"
                    side="bottom"
                    className='bg-white'
                >
                    <DropdownMenuItem>
                        <LogOut />
                        Выйти
                    </DropdownMenuItem>
                </DropdownMenuContent>
            </DropdownMenu>
        </div>
    );
};

export default AccountDropdown;
