import AccentHeading from '@/components/ui/AccentHeading';
import { Button } from '@/components/ui/Button';
import IndexToolbar from '@/components/ui/IndexToolbar';
import Pagination, { LaravelPaginator } from '@/components/ui/Pagination';
import Table from '@/components/ui/Table';
import { App } from '@/wayfinder/types';
import { Plus } from 'lucide-react';
import ExpoTable from './partials/ExpoTable';
import ExpoTableHeader from './partials/ExpoTableHeader';

type Props = {
    expos: LaravelPaginator<App.Models.Exhibition>;
    isSuperAdmin: boolean;
};

const Index = ({ expos, isSuperAdmin }: Props) => {
    return (
        <>
            <IndexToolbar>
                <AccentHeading className="text-xl">Выставки</AccentHeading>
                {isSuperAdmin && (
                    <Button>
                        <Plus /> Добавить выставку
                    </Button>
                )}
            </IndexToolbar>
            <Table
                isEmpty={expos?.data?.length === 0}
                placeholder="Пока что не создано ни одной выставки"
            >
                <ExpoTableHeader />
                <ExpoTable
                    expos={expos.data}
                    isSuperAdmin={isSuperAdmin}
                />
            </Table>
            <Pagination data={expos} />
        </>
    );
};

export default Index;
