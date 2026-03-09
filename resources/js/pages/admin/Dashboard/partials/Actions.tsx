import ActionCard from '@/components/ui/ActionCard';
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/Tooltip';
import { cn } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import CompanyController from '@/wayfinder/App/Http/Controllers/Admin/CompanyController';
import EventController from '@/wayfinder/App/Http/Controllers/Admin/EventController';
import { Link } from '@inertiajs/react';
import { Briefcase, Calendar, Plus, Star } from 'lucide-react';
import { FC } from 'react';

const Actions: FC<NodeProps<{ expoId: string | null }>> = ({
    className,
    expoId,
}) => {
    const disabled = !expoId || isNaN(Number(expoId));
    return disabled ? (
        <TooltipProvider delayDuration={0}>
            <Tooltip>
                <TooltipTrigger>
                    <ActionsContent
                        expoId={expoId}
                        className={className}
                    />
                </TooltipTrigger>
                <TooltipContent
                    side="left"
                    align="start"
                    className="p-4 text-xs text-foreground"
                >
                    <p className='max-w-50'>Выберите выставку для перехода на данную страницу</p>
                </TooltipContent>
            </Tooltip>
        </TooltipProvider>
    ) : (
        <ActionsContent expoId={expoId} />
    );
};

export default Actions;

const ActionsContent: FC<NodeProps<{ expoId: string | null }>> = ({
    className,
    expoId,
}) => {
    const disabled = !expoId || isNaN(Number(expoId));
    return (
        <ul
            className={cn(
                'flex flex-wrap items-start justify-center gap-3 lg:gap-6',
                disabled && 'opacity-50',
                className,
            )}
        >
            {actionCards.map((card) => (
                <ActionCard key={card.id}>
                    <ActionCard.Icon icon={card.icon} />
                    <ActionCard.Btn className="max-w-fit whitespace-nowrap">
                        <Link
                            href={disabled ? '' : card.url(Number(expoId))}
                            className={cn(disabled && 'pointer-events-none')}
                        >
                            <Plus />
                            {card.label}
                        </Link>
                    </ActionCard.Btn>
                </ActionCard>
            ))}
        </ul>
    );
};

const actionCards = [
    {
        id: crypto.randomUUID(),
        label: 'Добавить событие',
        icon: Calendar,
        url: (expoId: number) =>
            EventController.create({ exhibition: expoId }).url,
    },
    {
        id: crypto.randomUUID(),
        label: 'Добавить компанию',
        icon: Briefcase,
        url: (expoId: number) =>
            CompanyController.create({ exhibition: expoId }).url,
    },
    {
        id: crypto.randomUUID(),
        label: 'Создать общую задачу',
        icon: Star,
        url: (expoId: number) => '',
    },
];
