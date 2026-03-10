import Table from '@/components/ui/Table';
import TaskCard from '@/components/ui/TaskCard';
import { formatDateAndTime, getTaskStatus } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import { edit } from '@/wayfinder/routes/admin/exhibitions/all-tasks';
import { App } from '@/wayfinder/types';
import { Link } from '@inertiajs/react';
import { VisuallyHidden } from '@radix-ui/react-visually-hidden';
import { Eye, PencilLine } from 'lucide-react';
import { FC } from 'react';

const TaskTable: FC<
    NodeProps<{
        tasks: App.Models.Task[] | undefined;
        exhibition: App.Models.Exhibition;
    }>
> = ({ className, tasks, exhibition }) => {
    if (!tasks) {
        return null;
    }

    return (
        <Table.Body
            id="tasks-table"
            className={className}
        >
            {tasks.map((task) => (
                <Table.Row key={task.id}>
                    <Table.Cell
                        key="company"
                        width={2}
                    >
                        {task?.company?.public_name}
                    </Table.Cell>
                    <Table.Cell
                        key="title"
                        width={2}
                    >
                        {task.title}
                    </Table.Cell>
                    <Table.Cell
                        key="deadline"
                        width={1.4}
                    >
                        {formatDateAndTime(new Date(task.deadline))}
                    </Table.Cell>
                    <Table.Cell
                        width={0.5}
                        key="status"
                    >
                        <TaskCard.Badge
                            className="ml-0"
                            variant={getTaskStatus(task.status)}
                        >
                            {task.status}
                        </TaskCard.Badge>
                    </Table.Cell>
                    <Table.Cell
                        key="edit-btn"
                        width={0.5}
                    >
                        <Link
                            data-test={`edit-task-${task.id}`}
                            href={edit({
                                all_task: task.id,
                                exhibition: exhibition,
                            })}
                        >
                            <VisuallyHidden>
                                {task.status === 'На проверке'
                                    ? 'Редактировать задачу'
                                    : 'Посмотреть задачу'}{' '}
                            </VisuallyHidden>
                            {/* Status is to be verified */}
                            {task.status === 'На проверке' ? (
                                <PencilLine />
                            ) : (
                                <Eye />
                            )}
                        </Link>
                    </Table.Cell>
                </Table.Row>
            ))}
        </Table.Body>
    );
};

export default TaskTable;
