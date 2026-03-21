import AccentHeading from '@/components/ui/AccentHeading';
import IndexToolbar from '@/components/ui/IndexToolbar';
import Pagination from '@/components/ui/Pagination';
import Table from '@/components/ui/Table';
import { Inertia } from '@/wayfinder/types';
import { FC } from 'react';
import FollowupTable from './partials/FollowupTable';
import FollowupTableHeader from './partials/FollowupTableHeader';
import TaskCard from '@/components/ui/TaskCard';

const Index: FC<Inertia.Pages.Admin.Followups.Index> = ({
    followups
}) => {
    return (
        <div>
            <IndexToolbar className="items-center md:flex-col md:items-start">
                <AccentHeading className="text-xl">
                    Работа с партнерами
                </AccentHeading>
                <AccentHeading className="text-lg text-secondary 2xl:text-xl">
                    Услуги
                </AccentHeading>
            </IndexToolbar>

            <ul className="mb-12 grid w-full grid-cols-[repeat(auto-fill,minmax(10rem,1fr))] justify-items-center gap-3 sm:justify-items-start md:mb-14 2xl:grid-cols-[repeat(auto-fill,minmax(13.5rem,1fr))] 2xl:mb-18 xl:gap-6">
                    <TaskCard
                        className="w-40 2xl:w-55"
                    >
                        <TaskCard.Badge variant="success">
                            заявки на услуги
                        </TaskCard.Badge>
                        <TaskCard.Digit value={followups.data.length} />
                        <TaskCard.Label>{conjugateFollowup(followups.data.length)}</TaskCard.Label>
                    </TaskCard>
            </ul>

            <Table
                isEmpty={followups?.data?.length === 0}
                placeholder="По вашему запросу не найдено ни одной заявки на услугу"
            >
                <FollowupTableHeader />
                <FollowupTable
                    followups={followups.data}
                />
            </Table>
            <Pagination data={followups} />
        </div>
    );
};

export default Index;

function conjugateFollowup(count: number): string {
    const lastTwo = count % 100;
    const lastOne = count % 10;

    if (lastTwo >= 11 && lastTwo <= 19) {
        return 'заявок';
    }

    if (lastOne === 1) return 'заявка';
    if (lastOne >= 2 && lastOne <= 4) return 'заявки';
    return 'заявок';
}
