import ActionCard from '@/components/ui/ActionCard';
import { cn } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import { Briefcase, Calendar, Star } from 'lucide-react';
import { FC } from 'react';

const Actions: FC<NodeProps> = ({ className }) => {
    return (
        <ul
            className={cn(
                'flex flex-wrap items-start justify-center gap-3 lg:gap-6',
                className,
            )}
        >
            {actionCards.map((card) => (
                <ActionCard
                    key={card.id}
                >
                    <ActionCard.Icon icon={card.icon} />
                    <ActionCard.Btn
                        onClick={card.onClick}
                        className="max-w-fit whitespace-nowrap"
                    >
                        {card.label}
                    </ActionCard.Btn>
                </ActionCard>
            ))}
        </ul>
    );
};

export default Actions;

const actionCards = [
    {
        id: crypto.randomUUID(),
        label: 'Добавить событие',
        icon: Calendar,
        onClick: () => {},
    },
    {
        id: crypto.randomUUID(),
        label: 'Добавить компанию',
        icon: Briefcase,
        onClick: () => {},
    },
    {
        id: crypto.randomUUID(),
        label: 'Создать общую задачу',
        icon: Star,
        onClick: () => {},
    },
];
