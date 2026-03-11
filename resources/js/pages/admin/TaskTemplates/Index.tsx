import AccentHeading from '@/components/ui/AccentHeading';
import { Button } from '@/components/ui/Button';
import IndexToolbar from '@/components/ui/IndexToolbar';
import Pagination from '@/components/ui/Pagination';
import Table from '@/components/ui/Table';
import { Inertia } from '@/wayfinder/types';
import { Plus } from 'lucide-react';
import { FC } from 'react';
import TemplateTable from './partials/TemplateTable';
import TemplateTableHeader from './partials/TemplateTableHeader';
import { Link } from '@inertiajs/react';
import { create } from '@/wayfinder/routes/admin/exhibitions/task-templates';

const Index: FC<Inertia.Pages.Admin.TaskTemplates.Index> = ({
    templates,
    exhibition,
}) => {
    return (
        <>
            <IndexToolbar>
                <div>
                    <AccentHeading className="mb-2 text-xl sm:mb-3 xl:mb-5">
                        Общие задачи
                    </AccentHeading>
                    <AccentHeading className="text-lg text-secondary 2xl:text-xl">
                        Услуги
                    </AccentHeading>
                </div>
                <Button asChild>
                    <Link
                        data-test="create-task"
                        href={create({ exhibition })}
                    >
                        <Plus />
                        Добавить задачу
                    </Link>
                </Button>
            </IndexToolbar>

            <Table
                isEmpty={templates?.data?.length === 0}
                placeholder="По вашему запросу не найдено ни одной общейс задачи"
            >
                <TemplateTableHeader />
                <TemplateTable
                    templates={templates.data}
                    exhibition={exhibition}
                />
            </Table>
            <Pagination data={templates} />
        </>
    );
};

export default Index;
