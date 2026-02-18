import Table from '@/components/ui/Table';
import { getSortUrl } from '@/lib/utils';
import { Link } from '@inertiajs/react';
import { ChevronsUpDown } from 'lucide-react';

type Column = { label: string; query?: string };

const HEADER_COLS: Column[] = [
    { label: 'Фото' },
    { label: 'Имя', query: 'name' },
    { label: 'Роли'},
    { label: 'Телеграм'},
    { label: 'Кол-во лекций' },
    { label: 'Действия' },
] as const;

const EventTableHeader = () => {
    return (
        <Table.Header>
            <Table.Row>
                {HEADER_COLS.map((col, idx) => (
                    <Table.HeaderCell
                        key={idx}
                    >
                        {col.query ? (
                            <Link
                                href={getSortUrl(col.query)}
                                className="inline-flex items-center gap-2"
                                preserveScroll
                            >
                                {col.label}
                                <ChevronsUpDown className="size-4 text-gray-500" />
                            </Link>
                        ) : (
                            <span>{col.label}</span>
                        )}
                    </Table.HeaderCell>
                ))}
            </Table.Row>
        </Table.Header>
    );
};

export default EventTableHeader;
