import AccentHeading from '@/components/ui/AccentHeading';
import IndexToolbar from '@/components/ui/IndexToolbar';
import Pagination from '@/components/ui/Pagination';
import Table from '@/components/ui/Table';
import TaskCard from '@/components/ui/TaskCard';
import { getTaskStatus } from '@/lib/utils';
import { Inertia } from '@/wayfinder/types';
import { FC } from 'react';
import TaskTable from './partials/TaskTable';
import TaskTableHeader from './partials/TaskTableHeader';

const Index: FC<Inertia.Pages.Admin.Tasks.Index> = ({
    tasks,
    summary,
}) => {
    return (
        <div>
            <IndexToolbar className="items-center md:flex-col md:items-start">
                <AccentHeading className="text-xl">
                    Работа с партнерами
                </AccentHeading>
                <AccentHeading className="text-lg text-secondary 2xl:text-xl">
                    Задачи на проверке
                </AccentHeading>
            </IndexToolbar>

            <ul className="mb-12 grid w-full grid-cols-[repeat(auto-fill,minmax(10rem,1fr))] justify-items-center gap-3 sm:justify-items-start md:mb-14 2xl:grid-cols-[repeat(auto-fill,minmax(13.5rem,1fr))] 2xl:mb-18 xl:gap-6">
                {summary.map((task) => (
                    <TaskCard
                        key={task.id}
                        className="w-40 2xl:w-55"
                    >
                        <TaskCard.Badge variant={getTaskStatus(task.status)}>
                            {task.status}
                        </TaskCard.Badge>
                        <TaskCard.Digit value={task.count} />
                        <TaskCard.Label>задач</TaskCard.Label>
                    </TaskCard>
                ))}
            </ul>
            <Table
                isEmpty={tasks?.data?.length === 0}
                placeholder="По вашему запросу не найдено ни одной задачи"
            >
                <TaskTableHeader />
                <TaskTable
                    tasks={tasks.data}
                />
            </Table>
            <Pagination data={tasks} />
        </div>
    );
};

export default Index;
