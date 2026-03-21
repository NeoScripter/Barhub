import TaskCard from '@/components/ui/TaskCard';
import { cn, getTaskStatus } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import { Inertia } from '@/wayfinder/types';
import { usePage } from '@inertiajs/react';
import { FC } from 'react';

const Tasks: FC<NodeProps> = ({ className }) => {
    const { tasks } = usePage<{ tasks: Inertia.Pages.Exponent.Dashboard }>()
        .props;
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
                    <TaskCard.Badge variant={getTaskStatus(task.status)}>
                        {task.status}
                    </TaskCard.Badge>
                    <TaskCard.Digit value={task.count} />
                    <TaskCard.Label>{conjugateTasks(task.count)}</TaskCard.Label>
                    <hr className="mx-auto block h-0.5 w-4/5 bg-gray-300 text-gray-400" />
                    <p className="text-center text-foreground">{task.title}</p>
                </TaskCard>
            ))}
        </ul>
    );
};

export default Tasks;


function conjugateTasks(count: number): string {
    const lastTwo = count % 100;
    const lastOne = count % 10;

    if (lastTwo >= 11 && lastTwo <= 19) {
        return 'задач';
    }

    if (lastOne === 1) return 'задача';
    if (lastOne >= 2 && lastOne <= 4) return 'задачи';
    return 'задач';
}
