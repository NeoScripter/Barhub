import { NavDrawerType, navItems, NavItemType } from '@/lib/data/navItems';
import { cn } from '@/lib/utils';
import { NodeProps } from '@/types/ui';
import { Link } from '@inertiajs/react';
import { ChevronDown } from 'lucide-react';
import { FC, useState } from 'react';
import AccountDropdown from './AccountDropdown';

const NavMenu: FC<NodeProps> = ({ className }) => {
    return (
        <div
            className={cn(
                'px-7.5 sm:pb-10 lg:max-w-81 lg:shadow-xl lg:pt-7.5 lg:rounded-xl lg:fixed lg:z-10 lg:bg-white lg:top-24 lg:left-10 lg:pl-7.5 lg:pr-12 lg:pb-9 xl:left-31 xl:top-36.5',
                className,
            )}
        >
            <Header className="lg:hidden" />

            <nav>
                <ul className="grid gap-6">
                    {navItems.map((item) => (
                        <NavItem
                            key={item.id}
                            item={item}
                        />
                    ))}
                </ul>
            </nav>
        </div>
    );
};

export default NavMenu;

const Header: FC<NodeProps> = ({ className }) => {
    return (
        <header className={className}>
            {' '}
            <AccountDropdown
                className="mb-12"
                email="admin@gmail.com"
            />
        </header>
    );
};

const NavItem: FC<{ item: NavItemType }> = ({ item }) => {
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
                    {item.label}
                </Link>
            </li>
        );
    }

    return (
        <NavDrawer
            item={item}
            className={baseClass}
        />
    );
};

const NavDrawer: FC<{ item: NavDrawerType; className?: string }> = ({
    item,
    className,
}) => {
    const [show, setShow] = useState(false);

    return (
        <li>
            <button
                onClick={() => setShow((p) => !p)}
                type="button"
                className={className}
            >
                <item.icon className="size-4.5 shrink-0" />
                {item.label}
                <ChevronDown
                    className={cn(
                        '0.3s size-5 transition-transform',
                        show ? 'rotate-180' : 'rotate-0',
                    )}
                />
            </button>
            <ul
                className={cn(
                    'grid gap-6 overflow-hidden text-secondary transition-[max-height,margin] duration-300 ease-in-out',
                    show ? 'mt-6 ml-10 max-h-250' : 'mt-0 ml-0 max-h-0',
                )}
            >
                {item.links.map((link) => (
                    <li
                        key={link.id}
                        className="0.25s w-fit transition-opacity hover:animate-jump hover:opacity-75"
                    >
                        <Link href={link.url}>{item.label}</Link>{' '}
                    </li>
                ))}
            </ul>
        </li>
    );
};
