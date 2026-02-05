import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/DropdownMenu';
import { useCurrentUrl } from '@/hooks/useCurrentUrl';
import { useIsTablet } from '@/hooks/useMobile';
import { NavDrawerType } from '@/lib/data/navItems';
import { cn } from '@/lib/utils';
import { Link } from '@inertiajs/react';
import { ChevronDown } from 'lucide-react';
import { FC, useState } from 'react';

interface NavDrawerProps {
    item: NavDrawerType;
    expanded: boolean;
    className?: string;
}

const NavDrawer: FC<NavDrawerProps> = ({ item, className, expanded }) => {
    const [isOpen, setIsOpen] = useState(false);
    const { whenCurrentUrl } = useCurrentUrl();
    const isTablet = useIsTablet();

    return (
        <li className={className}>
            <DropdownMenu onOpenChange={setIsOpen}>
                <DropdownMenuTrigger className="flex cursor-pointer items-center gap-2 whitespace-nowrap xl:gap-3">
                    <item.icon className="size-4.5 shrink-0 xl:size-5.5" />
                    {(expanded || isTablet) && item.label}
                    {expanded && (
                        <ChevronDown
                            className={cn(
                                'size-5 transition-transform duration-300',
                                isOpen && 'rotate-180',
                            )}
                        />
                    )}
                </DropdownMenuTrigger>
                <DropdownMenuContent
                    align="end"
                    side="bottom"
                    className="border-none bg-white px-6 py-4"
                >
                    <ul className="grid gap-3">
                        {item.links.map((link) => (
                            <DropdownMenuItem
                                key={link.id}
                                asChild
                            >
                                <li className="group w-fit text-secondary">
                                    <Link
                                        href={link.url}
                                        prefetch
                                        preserveState
                                        className={cn(
                                            'text-base transition-opacity duration-250 select-none group-hover:animate-jump group-hover:opacity-75',
                                            whenCurrentUrl(
                                                link.url,
                                                'text-primary',
                                            ),
                                        )}
                                    >
                                        {link.label}
                                    </Link>
                                </li>
                            </DropdownMenuItem>
                        ))}
                    </ul>
                </DropdownMenuContent>
            </DropdownMenu>
        </li>
    );
};

export default NavDrawer;
