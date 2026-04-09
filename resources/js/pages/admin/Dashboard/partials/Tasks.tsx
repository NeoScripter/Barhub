import TaskCard from '@/components/ui/TaskCard';
import { cn, getTaskStatus } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import PartnerController from '@/wayfinder/App/Http/Controllers/Admin/PartnerController';
import { App } from '@/wayfinder/types';
import { Link, usePage } from '@inertiajs/react';
import { FC } from 'react';

const listClass =
    'flex w-full flex-wrap items-start justify-center gap-3 lg:gap-6';
const cardClass = 'basis-40 xl:basis-55';

const Tasks: FC<NodeProps> = ({ className }) => {
    const { tasks } = usePage<{ tasks: App.Models.Task[] }>().props;

    return tasks?.length > 0 ? (
        <ul className={cn(listClass, className)}>
            {tasks.map((task) => (
                <TaskCard
                    key={task.id}
                    className={cn(cardClass, 'relative')}
                >
                    <Link
                        href={PartnerController.index().url}
                        className="absolute inset-0"
                    />
                    <TaskCard.Badge variant={getTaskStatus(task.status)}>
                        {task.status}
                    </TaskCard.Badge>
                    <TaskCard.Digit value={task.count} />
                    <TaskCard.Label>задач</TaskCard.Label>
                </TaskCard>
            ))}
        </ul>
    ) : (
        <p>Пока что не создано ни одной задачи</p>
    );
};

export default Tasks;
