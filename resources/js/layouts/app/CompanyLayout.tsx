import { Button } from '@/components/ui/Button';
import { useCurrentUrl } from '@/hooks/useCurrentUrl';
import { cn } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import { Link } from '@inertiajs/react';
import { FC } from 'react';

const CompanyLayout: FC<NodeProps> = ({ className, children }) => {
    return (
        <div className={className}>
            <nav>
                <ul className="mb-12 flex flex-wrap items-center justify-center gap-3 mx-auto max-w-7/10 md:max-w-full">
                    {navLinks.map((link) => (
                        <CompanyNavLink
                            key={link.id}
                            link={link}
                        />
                    ))}
                </ul>
            </nav>

            {children}
        </div>
    );
};

const CompanyNavLink: FC<{ link: CompanyLinkType }> = ({ link }) => {
    const { currentUrl } = useCurrentUrl();

    const path = extractPathToCurrentCompany(currentUrl) ?? '';

    const isActive = currentUrl.endsWith(link.url(path));
    return (
        <li>
            <Button asChild>
                <Link
                    href={link.url(path)}
                    className={cn('min-w-40', !isActive && 'bg-white hover:text-white! text-foreground! shadow-md!')}
                >
                    {link.label}
                </Link>
            </Button>
        </li>
    );
};

export default CompanyLayout;

type CompanyLinkType = {
    id: string;
    label: string;
    url: (path: string) => string;
};

const navLinks: CompanyLinkType[] = [
    {
        id: crypto.randomUUID(),
        label: 'Редактировать',
        url: (path: string) => `${path}/edit`,
    },
    {
        id: crypto.randomUUID(),
        label: 'Аккаунт',
        url: (path: string) => `${path}/exponents`,
    },
    {
        id: crypto.randomUUID(),
        label: 'Задачи',
        url: (path: string) => `${path}/tasks`,
    },
    {
        id: crypto.randomUUID(),
        label: 'Услуги',
        url: (path: string) => `${path}/services`,
    },
];

function extractPathToCurrentCompany(url: string): string | null {
    const match = url.match(/(admin\/exhibitions\/\d+\/companies\/\d+)/);
    return match ? match[1] : null;
}
