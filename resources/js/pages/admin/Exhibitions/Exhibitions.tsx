import AccentHeading from '@/components/ui/AccentHeading';
import { Button } from '@/components/ui/Button';
import Pagination, { LaravelPaginator } from '@/components/ui/Pagination';
import Table from '@/components/ui/Table';
import { App } from '@/wayfinder/types';
import { Plus } from 'lucide-react';
import ExpoTable from './partials/ExpoTable';

const HEADER_ROWS = [
    'Название',
    'Начало',
    'Конец',
    'Площадка',
    'Статус',
    'Действия',
];

type Props = {
    expos: LaravelPaginator<App.Models.Exhibition>;
    isSuperAdmin: boolean;
};

const Exhibitions = ({ expos, isSuperAdmin }: Props) => {
    return (
        <>
            <div className="flex flex-wrap items-center justify-between gap-4 py-6.5 sm:py-8 xl:py-9.5">
                <AccentHeading className="text-lg">Выставки</AccentHeading>
                {isSuperAdmin && (
                    <Button size={'lg'}>
                        <Plus /> Добавить выставку
                    </Button>
                )}
            </div>
            <Table>
                <Table.Header>
                    <Table.Row>
                        {HEADER_ROWS.map((label, idx) => (
                            <Table.HeaderCell width={idx === 0 ? 2 : 1}>
                                {label}
                            </Table.HeaderCell>
                        ))}
                    </Table.Row>
                </Table.Header>
                {expos.data ? <ExpoTable expos={expos.data} /> : null}
            </Table>
            <Pagination data={expos} />
        </>
    );
};

export default Exhibitions;
