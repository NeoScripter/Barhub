import CardLayout from '@/components/layout/CardLayout';
import { Button } from '@/components/ui/Button';
import { cn } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import { Briefcase, ListChecks, LucideIcon } from 'lucide-react';
import { FC } from 'react';

const padding = 'px-12 gap-4 py-8';

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
        <CardLayout className={padding}>
            <div className="flex items-center gap-2 sm:gap-3">
                <Icon className="size-5.5 sm:size-6 lg:size-7" />
                <span className="text-sm font-bold text-foreground sm:text-lg lg:text-xl">
                    {card.label}
                </span>
            </div>

            <Button
                className="min-w-40 sm:mr-auto"
                onClick={card.onClick}
            >
                {card.btnLabel}
            </Button>
        </CardLayout>
    );
};

type ActionDataType = {
    id: string;
    icon: LucideIcon;
    label: string;
    btnLabel: string;
    onClick: () => void;
};

const actionData: ActionDataType[] = [
    {
        id: crypto.randomUUID(),
        icon: Briefcase,
        label: 'Данные о компании',
        btnLabel: 'Заполнить',
        onClick: () => {},
    },
    {
        id: crypto.randomUUID(),
        icon: ListChecks,
        label: 'Материалы выставки',
        btnLabel: 'Ознакомиться',
        onClick: () => {},
    },
];
