import ActionCard from '@/components/ui/ActionCard';
import { cn } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import CompanyController from '@/wayfinder/App/Http/Controllers/Admin/CompanyController';
import EventController from '@/wayfinder/App/Http/Controllers/Admin/EventController';
import TaskTemplateController from '@/wayfinder/App/Http/Controllers/Admin/TaskTemplateController';
import { Link } from '@inertiajs/react';
import { Briefcase, Calendar, Plus, Star } from 'lucide-react';
import { FC } from 'react';

const Actions: FC<NodeProps> = ({
    className,
}) => {
    return (
        <ul
            className={cn(
                'relative flex flex-wrap items-start justify-center gap-3 lg:gap-6',
                className,
            )}
        >
            {actionCards.map((card) => (
                <ActionCard key={card.id}>
                    <ActionCard.Icon icon={card.icon} />
                    <ActionCard.Btn className="max-w-fit whitespace-nowrap">
                        <Link href={card.url}>
                            <Plus />
                            {card.label}
                        </Link>
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
        url: EventController.create().url,
    },
    {
        id: crypto.randomUUID(),
        label: 'Добавить компанию',
        icon: Briefcase,
        url: CompanyController.create().url,
    },
    {
        id: crypto.randomUUID(),
        label: 'Создать общую задачу',
        icon: Star,
        url: TaskTemplateController.create().url,
    },
];
