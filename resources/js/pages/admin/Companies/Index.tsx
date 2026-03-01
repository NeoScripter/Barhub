import AccentHeading from '@/components/ui/AccentHeading';
import { Button } from '@/components/ui/Button';
import IndexToolbar from '@/components/ui/IndexToolbar';
import Pagination from '@/components/ui/Pagination';
import SearchInput from '@/components/ui/SearchInput';
import Table from '@/components/ui/Table';
import { Inertia } from '@/wayfinder/types';
import { Link } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import { FC } from 'react';
import CompanyTable from './partials/CompanyTable';
import CompanyTableHeader from './partials/CompanyTableHeader';
import { create } from '@/wayfinder/routes/admin/exhibitions/companies';

const Index: FC<Inertia.Pages.Admin.Companies.Index> = ({
    companies,
    exhibition,
}) => {
    return (
        <>
            <IndexToolbar>
                <AccentHeading className="text-xl">Компании</AccentHeading>
                <SearchInput placeholder="Поиск по названию" />
                <Button asChild>
                    <Link href={create({ exhibition }).url}>
                        <Plus /> Добавить компанию
                    </Link>
                </Button>
            </IndexToolbar>

            <Table
                isEmpty={companies?.data?.length === 0}
                placeholder="По вашему запросу не найдено ни одной компании"
            >
                <CompanyTableHeader />
                <CompanyTable
                    companies={companies.data}
                    exhibition={exhibition}
                />
            </Table>

            <Pagination data={companies} />
        </>
    );
};

export default Index;
