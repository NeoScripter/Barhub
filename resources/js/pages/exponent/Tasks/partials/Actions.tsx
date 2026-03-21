import CardLayout from '@/components/layout/CardLayout';
import { Button } from '@/components/ui/Button';
import { cn } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import CompanyController from '@/wayfinder/App/Http/Controllers/Exponent/CompanyController';
import InfoItemController from '@/wayfinder/App/Http/Controllers/Exponent/InfoItemController';
import { Link } from '@inertiajs/react';
import { Briefcase, ListChecks, LucideIcon } from 'lucide-react';
import { FC } from 'react';

const Actions: FC<NodeProps> = ({ className }) => {
    return (
        <ul
            className={cn(
                className,
                'flex w-full flex-col flex-wrap items-center gap-4 lg:gap-6 sm:flex-row',
            )}
        >
            {actionData.map((card) => (
                <ActionCard
                    key={card.id}
                    card={card}
                />
            ))}
        </ul>
    );
};

export default Actions;

const ActionCard: FC<NodeProps<{ card: ActionDataType }>> = ({ card }) => {
    const Icon = card.icon;
    return (
        <CardLayout className="padding">
            <div className="flex items-center gap-2 sm:gap-3">
                <Icon className="size-5.5 sm:size-6 lg:size-7" />
                <span className="text-sm font-bold text-foreground sm:text-lg lg:text-xl">
                    {card.label}
                </span>
            </div>

            <Button
                asChild
                className="min-w-40 sm:mr-auto"
            >
                <Link href={card.url}>
                {card.btnLabel}
                </Link>
            </Button>
        </CardLayout>
    );
};

type ActionDataType = {
    id: string;
    icon: LucideIcon;
    label: string;
    btnLabel: string;
    url: string;
};

const actionData: ActionDataType[] = [
    {
        id: crypto.randomUUID(),
        icon: Briefcase,
        label: 'Данные о компании',
        btnLabel: 'Перейти',
        url: CompanyController.index().url,
    },
    {
        id: crypto.randomUUID(),
        icon: ListChecks,
        label: 'Материалы выставки',
        btnLabel: 'Ознакомиться',
        url: InfoItemController.index().url,
    },
];
