import Pagination from '@/components/ui/Pagination';
import Table from '@/components/ui/Table';
import CompanyLayout from '@/layouts/app/CompanyLayout';
import { Inertia } from '@/wayfinder/types';
import { FC } from 'react';
import TaskTable from './partials/TaskTable';
import TaskTableHeader from './partials/TaskTableHeader';

const Index: FC<Inertia.Pages.Admin.Tasks.Index> = ({ tasks, exhibition, company }) => {
    return (
        <CompanyLayout className="space-y-30!">
            <Table
                isEmpty={tasks?.data?.length === 0}
                placeholder="По вашему запросу не найдено ни одной задачи"
            >
                <TaskTableHeader />
                <TaskTable
                    tasks={tasks.data}
                    exhibition={exhibition}
                    company={company}
                />
            </Table>
            <Pagination data={tasks} />
        </CompanyLayout>
    );
};

export default Index;
