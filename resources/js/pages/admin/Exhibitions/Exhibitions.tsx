import Pagination, { LaravelPaginator } from '@/components/ui/Pagination';
import Table from '@/components/ui/Table';
import { App } from '@/wayfinder/types';
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
};

const Exhibitions = ({ expos }: Props) => {
    return (
        <>
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
