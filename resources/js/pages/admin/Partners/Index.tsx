import AccentHeading from '@/components/ui/AccentHeading';
import IndexToolbar from '@/components/ui/IndexToolbar';
import Pagination from '@/components/ui/Pagination';
import Table from '@/components/ui/Table';
import { Inertia } from '@/wayfinder/types';
import { FC } from 'react';
import TaskTable from './partials/TaskTable';
import TaskTableHeader from './partials/TaskTableHeader';

const Index: FC<Inertia.Pages.Admin.Tasks.Index> = ({ tasks, exhibition }) => {
    return (
        <div>
            <IndexToolbar className="md:flex-col md:items-start">
                <AccentHeading className="text-xl">Работа с партнерами</AccentHeading>
                <AccentHeading className="text-lg 2xl:text-xl text-secondary">Задачи на проверке</AccentHeading>
            </IndexToolbar>

            <Table
                isEmpty={tasks?.data?.length === 0}
                placeholder="По вашему запросу не найдено ни одной задачи"
            >
                <TaskTableHeader />
                <TaskTable
                    tasks={tasks.data}
                    exhibition={exhibition}
                />
            </Table>
            <Pagination data={tasks} />
        </div>
    );
};

export default Index;
