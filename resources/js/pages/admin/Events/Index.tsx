import AccentHeading from '@/components/ui/AccentHeading';
import { Button } from '@/components/ui/Button';
import IndexToolbar from '@/components/ui/IndexToolbar';
import Pagination from '@/components/ui/Pagination';
import SearchInput from '@/components/ui/SearchInput';
import Table from '@/components/ui/Table';
import { create } from '@/wayfinder/routes/admin/events';
import { Inertia } from '@/wayfinder/types';
import { Link } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import { FC } from 'react';
import EventTable from './partials/EventTable';
import EventTableHeader from './partials/EventTableHeader';
import StageDialog from './partials/StageDialog';
import ThemeDialog from './partials/ThemeDialog';

const Index: FC<Inertia.Pages.Admin.Events.Index> = ({ events }) => {
    return (
        <>
            <IndexToolbar className='justify-start'>
                <ThemeDialog />
                <StageDialog />
            </IndexToolbar>
            <IndexToolbar>
                <AccentHeading className="text-xl">
                    События выставки
                </AccentHeading>
                <SearchInput placeholder="Поиск события" />

                <Button asChild>
                    <Link href={create()}>
                        <Plus /> Добавить событие
                    </Link>
                </Button>
            </IndexToolbar>
            <Table
                isEmpty={events?.data?.length === 0}
                placeholder="По вашему запросу не найдено ни одного события"
            >
                <EventTableHeader />
                <EventTable events={events.data} />
            </Table>
            <Pagination data={events} />
        </>
    );
};

export default Index;
