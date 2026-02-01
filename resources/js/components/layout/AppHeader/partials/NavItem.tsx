import { NavItemType } from '@/lib/data/navItems';
import { cn } from '@/lib/utils';
import { Link } from '@inertiajs/react';
import { FC } from 'react';
import NavDrawer from './NavDrawer';

const NavItem: FC<{ item: NavItemType; expanded: boolean }> = ({
    item,
    expanded,
}) => {
    const baseClass = 'inline-flex text-secondary items-center gap-2';

    if (item.type === 'link') {
        return (
            <li>
                <Link
                    className={cn(
                        baseClass,
                        '0.25s w-fit transition-opacity hover:animate-jump hover:opacity-75',
                    )}
                    href={item.url}
                >
                    <item.icon className="size-4.5 shrink-0" />
                    {expanded && item.label}
                </Link>
            </li>
        );
    }

    return (
        <NavDrawer
            expanded={expanded}
            item={item}
            className={baseClass}
        />
    );
};

export default NavItem;
