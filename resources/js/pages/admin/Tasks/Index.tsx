import { Button } from '@/components/ui/Button';
import Pagination from '@/components/ui/Pagination';
import Table from '@/components/ui/Table';
import CompanyLayout from '@/layouts/app/CompanyLayout';
import { create } from '@/wayfinder/routes/admin/exhibitions/tasks';
import { Inertia } from '@/wayfinder/types';
import { Link } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import { FC } from 'react';
import TaskTable from './partials/TaskTable';
import TaskTableHeader from './partials/TaskTableHeader';

const Index: FC<Inertia.Pages.Admin.Tasks.Index> = ({
    tasks,
    exhibition,
    company,
}) => {
    const CreateLink = () => (
        <Button asChild>
            <Link href={create({ exhibition, company })}>
                <Plus />
                Создать задачу
            </Link>
        </Button>
    );
    return (
        <CompanyLayout
            className="space-y-30!"
            createLink={CreateLink}
        >
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
