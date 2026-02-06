import Table from '@/components/ui/Table';
import { getSortUrl } from '@/lib/utils';
import { Link, usePage } from '@inertiajs/react';
import { ChevronsUpDown } from 'lucide-react';

type Column = { label: string; query?: string };

const HEADER_COLS: Column[] = [
    { label: 'Название', query: 'name' },
    { label: 'Начало', query: 'starts_at' },
    { label: 'Конец', query: 'ends_at' },
    { label: 'Площадка', query: 'location' },
    { label: 'Статус', query: 'is_active' },
    { label: 'Действия' },
] as const;


const ExpoTableHeader = () => {
    const { isSuperAdmin } = usePage<{ isSuperAdmin: boolean }>().props;
    const columns = isSuperAdmin ? HEADER_COLS : HEADER_COLS.slice(0, -1);

    return (
        <Table.Header>
            <Table.Row>
                {columns.map((col, idx) => (
                    <Table.HeaderCell
                        key={idx}
                        width={idx === 0 ? 2 : 1}
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

export default ExpoTableHeader;
