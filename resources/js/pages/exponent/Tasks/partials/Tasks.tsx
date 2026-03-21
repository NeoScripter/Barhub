import TaskCard from '@/components/ui/TaskCard';
import { cn, getTaskStatus } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import { edit } from '@/wayfinder/routes/exponent/tasks';
import { App } from '@/wayfinder/types';
import { Link, usePage } from '@inertiajs/react';
import { FC } from 'react';

const Tasks: FC<NodeProps> = ({ className }) => {
    const { tasks } = usePage<{ tasks: App.Models.Task[] }>().props;

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
                    className="relative basis-40 border-2 transition-[scale,border] hover:scale-105 hover:border-primary xl:basis-55"
                >
                    <Link
                        href={edit({ task }).url}
                        className="absolute inset-0"
                    />
                    <TaskCard.Badge variant={getTaskStatus(task.status)}>
                        {task.status}
                    </TaskCard.Badge>
                    <TaskCard.Digit value={task.date} />
                    <TaskCard.Label>{task.month}</TaskCard.Label>
                    <hr className="mx-auto block h-0.5 w-4/5 bg-gray-300 text-gray-400" />
                    <p className="text-center text-foreground">{task.title}</p>
                </TaskCard>
            ))}
        </ul>
    );
};

export default Tasks;
