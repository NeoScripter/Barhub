import TaskCard from '@/components/ui/TaskCard';
import { cn } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import { FC } from 'react';

const Tasks: FC<NodeProps> = ({ className }) => {
    return (
        <ul
            className={cn(
                className,
                'flex w-full flex-wrap items-start justify-center gap-3 md:justify-start lg:gap-6',
            )}
        >
            {tasks.map((task) => (
                <TaskCard
                    key={task.id}
                    className="basis-40 xl:basis-55"
                >
                    <TaskCard.Badge variant={task.variant}>
                        {task.status}
                    </TaskCard.Badge>
                    <TaskCard.Digit value={task.number} />
                    <TaskCard.Label>задач</TaskCard.Label>
                </TaskCard>
            ))}
        </ul>
    );
};

export default Tasks;

const tasks = [
    {
        id: crypto.randomUUID(),
        status: 'просрочено',
        variant: 'danger',
        number: 5,
    },
    {
        id: crypto.randomUUID(),
        status: 'на проверке',
        variant: 'warning',
        number: 16,
    },
    {
        id: crypto.randomUUID(),
        status: 'требуют доработки',
        variant: 'default',
        number: 10,
    },
    {
        id: crypto.randomUUID(),
        status: 'просрочено',
        variant: 'danger',
        number: 5,
    },
    {
        id: crypto.randomUUID(),
        status: 'на проверке',
        variant: 'warning',
        number: 16,
    },
    {
        id: crypto.randomUUID(),
        status: 'требуют доработки',
        variant: 'default',
        number: 10,
    },
    {
        id: crypto.randomUUID(),
        status: 'на проверке',
        variant: 'warning',
        number: 16,
    },
];
