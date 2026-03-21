import AccentHeading from '@/components/ui/AccentHeading';
import { Button } from '@/components/ui/Button';
import Image from '@/components/ui/Image';
import { useCurrentUrl } from '@/hooks/useCurrentUrl';
import { cn } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import { Inertia } from '@/wayfinder/types';
import { Link, usePage } from '@inertiajs/react';
import { FC } from 'react';

const CompanyLayout: FC<NodeProps<{ createLink?: React.ComponentType }>> = ({
    className,
    children,
    createLink: CreateLink,
}) => {
    const { company } = usePage<Inertia.Pages.Admin.Companies.Edit>().props;
    const { currentUrl } = useCurrentUrl();

    return (
        <div>
            <nav>
                <ul className="mx-auto mt-8 mb-24 flex max-w-7/10 flex-wrap items-center justify-center gap-3 md:max-w-full">
                    {navLinks.map((link) => (
                        <NavLink
                            key={link.id}
                            link={link}
                        />
                    ))}
                </ul>
            </nav>

            {!currentUrl.endsWith('edit') && (
                <div className="my-20 flex flex-col items-center justify-between gap-4 sm:flex-row">
                    <div className="flex flex-col items-center gap-4 sm:flex-row sm:gap-9">
                        {company.logo && (
                            <Image
                                wrapperStyles="max-w-27 md:max-w-40"
                                image={company.logo}
                            />
                        )}

                        <div className="text-center sm:text-left">
                            <AccentHeading
                                asChild
                                className="mb-1 text-lg"
                            >
                                <h3>{company.public_name}</h3>
                            </AccentHeading>

                            <p>{company.legal_name}</p>
                        </div>
                    </div>
                    {CreateLink && <CreateLink />}
                </div>
            )}

            <div className={className}>{children}</div>
        </div>
    );
};

const NavLink: FC<{ link: NavLinkType }> = ({ link }) => {
    const { currentUrl } = useCurrentUrl();

    const path = extractPathToCurrentCompany(currentUrl) ?? '';

    const isActive = currentUrl.endsWith(link.url(path));

    return (
        <li>
            <Button asChild>
                <Link
                    href={link.url(path)}
                    className={cn(
                        'min-w-40',
                        !isActive &&
                            'bg-white text-foreground! shadow-md! hover:text-white!',
                    )}
                >
                    {link.label}
                </Link>
            </Button>
        </li>
    );
};

export default CompanyLayout;

type NavLinkType = {
    id: string;
    label: string;
    url: (path: string) => string;
};

const navLinks: NavLinkType[] = [
    {
        id: crypto.randomUUID(),
        label: 'Редактировать',
        url: (path: string) => `/${path}/edit`,
    },
    {
        id: crypto.randomUUID(),
        label: 'Аккаунт',
        url: (path: string) => `/${path}/exponents`,
    },
    {
        id: crypto.randomUUID(),
        label: 'Задачи',
        url: (path: string) => `/${path}/tasks`,
    },
    {
        id: crypto.randomUUID(),
        label: 'Услуги',
        url: (path: string) => `/${path}/followups`,
    },
];

function extractPathToCurrentCompany(url: string): string | null {
    const match = url.match(/(admin\/companies\/\d+)/);
    return match ? match[1] : null;
}
