import AccentHeading from '@/components/ui/AccentHeading';
import { useCurrentUrl } from '@/hooks/useCurrentUrl';
import { renderNavItems } from '@/lib/data/navItems';
import { cn } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import { App } from '@/wayfinder/types';
import { usePage } from '@inertiajs/react';
import { ArrowLeftToLine } from 'lucide-react';
import { FC, useEffect, useState } from 'react';
import AccountDropdown from './AccountDropdown';
import NavItem from './NavItem';

const NavMenu: FC<NodeProps> = ({ className }) => {
    const [expanded, setExpanded] = useState(false);
    const { currentUrl } = useCurrentUrl();

    useEffect(() => {
        const collapseMenu = () => setExpanded(false);
        document.addEventListener('closeNavMenu', collapseMenu);

        return () => document.removeEventListener('closeNavMenu', collapseMenu);
    }, []);

    return (
        <div
            className={cn(
                'group w-full px-7.5 duration-300 ease-in-out sm:py-8 sm:transition-all lg:fixed lg:top-10 lg:left-10 lg:z-10 lg:max-w-12.5 lg:rounded-xl lg:border lg:border-gray-200/40 lg:bg-white lg:px-4.5 lg:py-7 lg:shadow-xl hover:lg:max-w-81 hover:lg:py-9 hover:lg:pr-12 hover:lg:pl-7.5 2xl:left-31',
                className,
            )}
        >
            <Header className="lg:hidden" />

            <nav>
                <ul
                    className={cn(
                        'grid gap-6 not-group-hover:lg:place-content-center not-group-hover:lg:place-items-center',
                    )}
                >
                    {renderNavItems(currentUrl).map((item) => (
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
    const { exhibition, auth } = usePage<{
        exhibition: App.Models.Exhibition | null;
        auth: ShareData;
    }>().props;

    return (
        <header className={className}>
            {' '}
            {auth?.user && (
                <AccountDropdown
                    className="mb-8"
                    email={auth.user?.email}
                />
            )}
            {exhibition && (
                <AccentHeading
                    asChild
                    className="mb-8 text-lg lg:hidden"
                >
                    <p>{exhibition.name}</p>
                </AccentHeading>
            )}
        </header>
    );
};

const CollapseBtn: FC<{ expanded: boolean; handleClick: () => void }> = ({
    expanded,
    handleClick,
}) => {
    return (
        <div className="mb-6 hidden lg:block">
            <button
                onClick={handleClick}
                className="flex items-center gap-3 text-sm text-secondary xl:text-base"
                data-test="collapse-menu-button"
            >
                <ArrowLeftToLine
                    className={cn(
                        'size-4.5 transition-transform duration-300 ease-in-out xl:size-5.5',
                        !expanded &&
                            'rotate-y-180 lg:-translate-x-0.5 xl:-translate-x-1',
                    )}
                />
                {expanded && 'Свернуть'}
            </button>
            <hr className="mt-3 text-secondary/90" />
        </div>
    );
};
