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
    people,
    exhibition,
}) => {
    return (
        <>
            <IndexToolbar>
                <AccentHeading className="text-xl">Люди</AccentHeading>
                <SearchInput placeholder="Поиск по имени" />

                <Button>
                    <Plus /> Добавить человека
                </Button>
            </IndexToolbar>
            <Table
                isEmpty={people?.data?.length === 0}
                placeholder="По вашему запросу не найдено ни одного человека"
            >
                <PersonTableHeader />
                <PersonTable
                    people={people.data}
                    exhibition={exhibition}
                />
            </Table>
            <Pagination data={people} />
        </>
    );
};

export default Index;
