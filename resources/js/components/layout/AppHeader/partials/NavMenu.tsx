import AccentHeading from '@/components/ui/AccentHeading';
import { useCurrentUrl } from '@/hooks/useCurrentUrl';
import useMediaQuery from '@/hooks/useMediaQuery';
import { renderNavItems } from '@/lib/data/navItems';
import { cn } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import { App, Inertia } from '@/wayfinder/types';
import { usePage } from '@inertiajs/react';
import { ArrowLeftToLine } from 'lucide-react';
import { FC, useState } from 'react';
import AccountDropdown from './AccountDropdown';
import NavItem from './NavItem';

const NavMenu: FC<NodeProps> = ({ className }) => {
    const [expanded, setExpanded] = useState(false);
    const { currentUrl } = useCurrentUrl();
    const { auth } = usePage<{ auth: Inertia.SharedData }>().props;

    return (
        <div
            className={cn(
                'w-full px-7.5 duration-300 ease-in-out sm:pb-8 sm:transition-all lg:fixed lg:top-10 lg:left-10 lg:z-10 lg:rounded-xl lg:border lg:border-gray-200/40 lg:bg-white lg:pt-5 lg:shadow-xl xl:left-31',
                {
                    'lg:max-w-81 lg:pr-12 lg:pb-9 lg:pl-7.5': expanded,
                    'lg:max-w-12.5 lg:px-4.5 lg:pb-7': !expanded,
                },
                className,
            )}
        >
            <Header className="lg:hidden" />

            <CollapseBtn
                expanded={expanded}
                handleClick={() => setExpanded((p) => !p)}
            />

            <nav>
                <ul
                    className={cn(
                        'grid gap-6',
                        !expanded &&
                            'lg:place-content-center lg:place-items-center',
                    )}
                >
                    {renderNavItems(currentUrl, auth.canViewAnyExhibitions).map(
                        (item) => (
                            <NavItem
                                expanded={expanded}
                                key={item.id}
                                item={item}
                            />
                        ),
                    )}
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
