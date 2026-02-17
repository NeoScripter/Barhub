import AccentHeading from '@/components/ui/AccentHeading';
import { Button } from '@/components/ui/Button';
import Pagination from '@/components/ui/Pagination';
import Table from '@/components/ui/Table';
import { Inertia } from '@/wayfinder/types';
import { Plus } from 'lucide-react';
import { FC } from 'react';
import EventTableHeader from './partials/EventTableHeader';
import EventTable from './partials/EventTable';

const Index: FC<Inertia.Pages.Admin.Events.Index> = ({
    events,
    exhibition
}) => {
    return (
        <>
            <div className="flex flex-wrap items-center justify-between gap-4 py-6.5 sm:py-8 xl:py-9.5">
                <AccentHeading className="text-lg">События</AccentHeading>
                <Button size={'lg'}>
                    <Plus /> Добавить событие
                </Button>
            </div>
            <Table>
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
