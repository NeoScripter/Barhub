import AccentHeading from '@/components/ui/AccentHeading';
import { Button } from '@/components/ui/Button';
import IndexToolbar from '@/components/ui/IndexToolbar';
import Pagination from '@/components/ui/Pagination';
import SearchInput from '@/components/ui/SearchInput';
import Table from '@/components/ui/Table';
import { Inertia } from '@/wayfinder/types';
import { Plus } from 'lucide-react';
import { FC } from 'react';
import PersonTable from './partials/PersonTable';
import PersonTableHeader from './partials/PersonTableHeader';

const Index: FC<Inertia.Pages.Admin.People.Index> = ({
    persons,
    exhibition,
}) => {
    return (
        <>
            <IndexToolbar>
                <AccentHeading className="text-xl">События выставки</AccentHeading>
                <SearchInput placeholder="Поиск события" />

                <Button>
                    <Plus /> Добавить событие
                </Button>
            </IndexToolbar>
            <Table
                isEmpty={persons?.data?.length === 0}
                placeholder="По вашему запросу не найдено ни одного события"
            >
                <PersonTableHeader />
                <PersonTable
                    persons={persons.data}
                    exhibition={exhibition}
                />
            </Table>
            <Pagination data={persons} />
        </>
    );
};

export default Index;
