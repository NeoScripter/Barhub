import Pagination from '@/components/ui/Pagination';
import Table from '@/components/ui/Table';
import { Inertia } from '@/wayfinder/types';
import { FC } from 'react';
import TaskTable from './partials/TaskTable';
import TaskTableHeader from './partials/TaskTableHeader';

const Index: FC<Inertia.Pages.Admin.Tasks.Index> = ({ tasks, exhibition }) => {
    return (
        <div>
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
