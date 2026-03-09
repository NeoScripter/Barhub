import TaskCard from '@/components/ui/TaskCard';
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/Tooltip';
import { cn } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import { App } from '@/wayfinder/types';
import { usePage } from '@inertiajs/react';
import { FC } from 'react';
import { getTaskStatus } from '../../Tasks/partials/TaskTable';

const PLACEHOLDER_STATUSES = ['просрочено', 'на проверке', 'требуют доработки'];

const listClass =
    'flex w-full flex-wrap items-start justify-center gap-3 lg:gap-6';
const cardClass = 'basis-40 xl:basis-55';

const Tasks: FC<NodeProps> = ({ className }) => {
    const { tasks } = usePage<{ tasks: App.Models.Task[] }>().props;

    return (
        <ul className={cn(listClass, className)}>
            {tasks?.length
                ? tasks.map((task) => (
                      <TaskCard
                          key={task.id}
                          className={cardClass}
                      >
                          <TaskCard.Badge variant={getTaskStatus(task.status)}>
                              {task.status}
                          </TaskCard.Badge>
                          <TaskCard.Digit value={task.count} />
                          <TaskCard.Label>задач</TaskCard.Label>
                      </TaskCard>
                  ))
                : PLACEHOLDER_STATUSES.map((status) => (
                      <PlaceholderCard
                          key={status}
                          status={status}
                      />
                  ))}
        </ul>
    );
};

export default Tasks;

const PlaceholderCard: FC<{ status: string }> = ({ status }) => (
    <TooltipProvider delayDuration={0}>
        <Tooltip>
            <TaskCard className={cn(cardClass, 'relative opacity-50')}>
                    <TaskCard.Badge>{status}</TaskCard.Badge>
                    <TaskCard.Digit value="?" />
                    <TaskCard.Label>задач</TaskCard.Label>
                <TooltipTrigger className="absolute inset-0"/>
                <TooltipContent
                    side="left"
                    align="start"
                    className="p-4 text-xs text-foreground"
                >
                    <p className="max-w-50">
                        Выберите выставку для просмотра задач
                    </p>
                </TooltipContent>
            </TaskCard>
        </Tooltip>
    </TooltipProvider>
);
