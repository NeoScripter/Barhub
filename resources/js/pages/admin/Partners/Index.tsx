import AccentHeading from '@/components/ui/AccentHeading';
import { Button } from '@/components/ui/Button';
import IndexToolbar from '@/components/ui/IndexToolbar';
import Pagination from '@/components/ui/Pagination';
import Table from '@/components/ui/Table';
import TaskCard from '@/components/ui/TaskCard';
import {
    cn,
    getSingleFilterUrl,
    getTaskStatus,
    isActiveFilter,
} from '@/lib/utils';
import { Inertia } from '@/wayfinder/types';
import { Link } from '@inertiajs/react';
import { Download } from 'lucide-react';
import { FC } from 'react';
import TaskTable from './partials/TaskTable';
import TaskTableHeader from './partials/TaskTableHeader';

const Index: FC<Inertia.Pages.Admin.Tasks.Index> = ({ tasks, summary }) => {

    const params = new URLSearchParams(window.location.search);
    const exportUrl = `/admin/tasks/export?${params.toString()}`;

    return (
        <div>
            <div className="heading flex items-center justify-between gap-3">
                <IndexToolbar className="items-center md:flex-col md:items-start">
                    <AccentHeading className="text-xl">
                        Работа с партнерами
                    </AccentHeading>
                    <AccentHeading className="text-lg text-secondary 2xl:text-xl">
                        Задачи на проверке
                    </AccentHeading>
                </IndexToolbar>

                <Button
                    variant="ghost"
                    asChild
                    size="lg"
                >
                    <a href={exportUrl}>
                        <Download />
                        Скачать список
                    </a>
                </Button>
            </div>

            <ul className="mb-12 grid w-full grid-cols-[repeat(auto-fill,minmax(10rem,1fr))] justify-items-center gap-3 sm:justify-items-start md:mb-14 xl:gap-6 2xl:mb-18 2xl:grid-cols-[repeat(auto-fill,minmax(13.5rem,1fr))]">
                {summary.map((task) => (
                    <TaskCard
                        key={task.id}
                        className={cn(
                            'relative w-40 2xl:w-55',
                            isActiveFilter('status', task.rawStatus) &&
                                'ring-2 ring-primary/70',
                        )}
                    >
                        <Link
                            href={getSingleFilterUrl('status', task.rawStatus)}
                            className={cn('absolute inset-0')}
                        />
                        <TaskCard.Badge variant={getTaskStatus(task.status)}>
                            {task.status}
                        </TaskCard.Badge>
                        <TaskCard.Digit value={task.count} />
                        <TaskCard.Label>
                            {conjugateTasks(task.count)}
                        </TaskCard.Label>
                    </TaskCard>
                ))}
            </ul>
            <Table
                isEmpty={tasks?.data?.length === 0}
                placeholder="По вашему запросу не найдено ни одной задачи"
            >
                <TaskTableHeader />
                <TaskTable tasks={tasks.data} />
            </Table>
            <Pagination data={tasks} />
        </div>
    );
};

export default Index;

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
