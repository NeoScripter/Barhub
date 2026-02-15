import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/DropdownMenu';
import { logout } from '@/wayfinder/routes';
import { Link, router } from '@inertiajs/react';
import { ChevronDown, LogOut } from 'lucide-react';
import { FC } from 'react';

const AccountDropdown: FC<{ email: string; className?: string }> = ({
    email,
    className,
}) => {
    return (
        <div className={className}>
            <DropdownMenu>
                <DropdownMenuTrigger className="flex cursor-pointer items-center gap-[0.5em] font-medium text-primary 2xl:text-xl">
                    {email}
                    <ChevronDown
                        className="w-[1em]"
                        strokeWidth={3}
                    />
                </DropdownMenuTrigger>

                <DropdownMenuContent
                    align="end"
                    side="bottom"
                    className="bg-white"
                >
                    <DropdownMenuItem asChild>
                        <Link
                            href={logout()}
                            onSuccess={() => router.flushAll()}
                        >
                            <LogOut />
                            Выйти
                        </Link>
                    </DropdownMenuItem>
                </DropdownMenuContent>
            </DropdownMenu>
        </div>
    );
};

export default AccountDropdown;
