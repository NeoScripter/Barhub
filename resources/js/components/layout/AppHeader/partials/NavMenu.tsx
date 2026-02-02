import AccentHeading from '@/components/ui/AccentHeading';
import { navItems } from '@/lib/data/navItems';
import { cn } from '@/lib/utils';
import { NodeProps } from '@/types/ui';
import { ArrowLeftToLine } from 'lucide-react';
import { FC, useState } from 'react';
import AccountDropdown from './AccountDropdown';
import NavItem from './NavItem';

const NavMenu: FC<NodeProps> = ({ className }) => {
    const [expanded, setExpanded] = useState(true);

    return (
        <div
            className={cn(
                'px-7.5 duration-300 ease-in-out sm:pb-8 sm:transition-all lg:fixed lg:top-24 lg:left-10 lg:z-10 lg:rounded-xl lg:border lg:border-gray-200/40 lg:bg-white lg:pt-5 lg:shadow-xl xl:top-36.5 xl:left-31',
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
                    {navItems.map((item) => (
                        <NavItem
                            expanded={expanded}
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
                className="mb-8"
                email="admin@gmail.com"
            />
            <AccentHeading
                asChild
                className="mb-8 text-lg lg:hidden"
            >
                <p>Название выставки</p>
            </AccentHeading>
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
            >
                <ArrowLeftToLine
                    className={cn(
                        'size-4.5 transition-transform duration-300 ease-in-out xl:size-5.5',
                        !expanded && 'lg:-translate-x-0.5 xl:-translate-x-1 rotate-y-180',
                    )}
                />
                {expanded && 'Свернуть'}
            </button>
            <hr className="mt-3 text-secondary/90" />
        </div>
    );
};
