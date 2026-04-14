import Table from '@/components/ui/Table';
import TaskCard from '@/components/ui/TaskCard';
import { formatDateAndTime, getTaskStatus } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import PartnerController from '@/wayfinder/App/Http/Controllers/Admin/PartnerController';
import TaskController from '@/wayfinder/App/Http/Controllers/Admin/TaskController';
import { App } from '@/wayfinder/types';
import { Link } from '@inertiajs/react';
import { VisuallyHidden } from '@radix-ui/react-visually-hidden';
import { Eye, PencilLine } from 'lucide-react';
import { FC } from 'react';

const TaskTable: FC<
    NodeProps<{
        tasks: App.Models.Task[] | undefined;
        company: App.Models.Company;
    }>
> = ({ className, tasks, company }) => {
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
                        key="title"
                        width={2}
                    >
                        <Link
                            data-test={`edit-task-${task.id}`}
                            href={
                                task.status === 'Ожидает выполнения'
                                    ? TaskController.edit({
                                          task: task.id,
                                          company: company,
                                      })
                                    : PartnerController.edit({
                                          all_task: task.id,
                                      })
                            }
                            className="absolute inset-0"
                        />
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
                </Table.Row>
            ))}
        </Table.Body>
    );
};

export default TaskTable;
