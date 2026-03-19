import AccentHeading from '@/components/ui/AccentHeading';
import IndexToolbar from '@/components/ui/IndexToolbar';
import Pagination from '@/components/ui/Pagination';
import Table from '@/components/ui/Table';
import { Inertia } from '@/wayfinder/types';
import { FC } from 'react';
import FollowupTable from './partials/FollowupTable';
import FollowupTableHeader from './partials/FollowupTableHeader';

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

            <Table
                isEmpty={followups?.data?.length === 0}
                placeholder="По вашему запросу не найдено ни одной услуги"
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
