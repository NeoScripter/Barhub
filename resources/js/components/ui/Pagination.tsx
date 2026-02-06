import { cn } from '@/lib/utils';
import { Link } from '@inertiajs/react';
import { ChevronLeftIcon, ChevronRightIcon } from 'lucide-react';

type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

export type LaravelPaginator<T> = {
    data: T[];
    current_page: number;
    first_page_url: string;
    from: number | null;
    last_page: number;
    last_page_url: string;
    links: PaginationLink[];
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number | null;
    total: number;
};

type PaginationProps<T> = {
    data: LaravelPaginator<T>;
    label?: string;
    className?: string;
};

const Pagination = <T,>({ data, label, className }: PaginationProps<T>) => {
    const { links, from, to, total } = data;

    const onePage = !links || links.length === 0;
    const emptyList = data?.data?.length === 0;

    if (onePage || emptyList) return null;

    return (
        <div
            className={cn(
                'flex items-center justify-between py-7 md:py-10 xl:py-14',
                className,
            )}
        >
            {/* Mobile Navigation */}
            <div className="mx-auto flex max-w-8/10 flex-1 justify-between sm:hidden">
                <PaginationButton
                    link={links[0]}
                    className="size-12"
                >
                    <ChevronLeftIcon className="size-16 text-foreground" />
                </PaginationButton>

                <PaginationButton
                    link={links[links.length - 1]}
                    className="size-12"
                >
                    <ChevronRightIcon className="size-16 text-foreground" />
                </PaginationButton>
            </div>

            {/* Desktop Navigation */}
            <div className="hidden flex-wrap sm:flex sm:flex-1 sm:items-center sm:justify-center">
                {/* Optional label */}
                {label && from && to && total && (
                    <div className="hidden">
                        <p className="text-sm font-medium 2xl:text-base">
                            Showing {label} {from} to {to} of {total} results
                        </p>
                    </div>
                )}

                <nav
                    aria-label="Pagination"
                    className="isolate flex items-center justify-center gap-2 2xl:gap-3"
                >
                    {links.map((link, index) => {
                        const isFirst = index === 0;
                        const isLast = index === links.length - 1;

                        return (
                            <PaginationButton
                                key={`pagination-${index}`}
                                link={link}
                            >
                                {isFirst ? (
                                    <ChevronLeftIcon className="size-6 text-foreground 2xl:size-8" />
                                ) : isLast ? (
                                    <ChevronRightIcon className="size-6 text-foreground 2xl:size-8" />
                                ) : (
                                    link.label
                                )}
                            </PaginationButton>
                        );
                    })}
                </nav>
            </div>
        </div>
    );
};

type PaginationButtonProps = {
    link: PaginationLink;
    children: React.ReactNode;
    className?: string;
};

const PaginationButton: React.FC<PaginationButtonProps> = ({
    link,
    children,
    className,
}) => {
    const baseClasses = cn(
        'relative inline-flex size-8 items-center justify-center rounded-sm font-medium ring-1 transition duration-200 ease-in ring-inset 2xl:size-10 2xl:text-xl',
        {
            'bg-primary text-white ring-muted-foreground': link.active,
            'text-foreground ring-inherit hover:scale-110':
                !link.active && link.url,
            'opacity-50': !link.url,
        },
        className,
    );

    // Disabled state
    if (!link.url) {
        return <span className={baseClasses}>{children}</span>;
    }

    // Active state (current page)
    if (link.active) {
        return <span className={baseClasses}>{children}</span>;
    }

    // Clickable link
    return (
        <Link
            href={link.url}
            className={baseClasses}
            preserveScroll
            preserveState
        >
            {children}
        </Link>
    );
};

export default Pagination;
