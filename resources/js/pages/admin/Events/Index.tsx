import AccentHeading from '@/components/ui/AccentHeading';
import { Button } from '@/components/ui/Button';
import IndexToolbar from '@/components/ui/IndexToolbar';
import Pagination from '@/components/ui/Pagination';
import SearchInput from '@/components/ui/SearchInput';
import Table from '@/components/ui/Table';
import { Inertia } from '@/wayfinder/types';
import { Plus } from 'lucide-react';
import { FC } from 'react';
import EventTable from './partials/EventTable';
import EventTableHeader from './partials/EventTableHeader';

const Index: FC<Inertia.Pages.Admin.Events.Index> = ({
    events,
    exhibition,
}) => {
    return (
        <>
            <IndexToolbar>
                <AccentHeading className="text-xl">События</AccentHeading>
                <SearchInput placeholder="Поиск события" />

                <Button>
                    <Plus /> Добавить событие
                </Button>
            </IndexToolbar>
            <Table
                isEmpty={events?.data?.length === 0}
                placeholder="По вашему запросу не найдено ни одного события"
            >
                <EventTableHeader />
                <EventTable
                    events={events.data}
                    exhibition={exhibition}
                />
            </Table>
            <Pagination data={events} />
        </>
    );
};

export default Index;
