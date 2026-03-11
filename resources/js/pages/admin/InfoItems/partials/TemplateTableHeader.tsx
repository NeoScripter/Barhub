import Table from '@/components/ui/Table';
import { getSortUrl } from '@/lib/utils';
import { Link } from '@inertiajs/react';
import { ChevronsUpDown } from 'lucide-react';

type Column = { label: string; query?: string };

const HEADER_COLS: Column[] = [
    { label: 'Заголовок', query: 'title' },
    { label: 'Дедлайн', query: 'deadline' },
    { label: 'Действия' },
] as const;

const TemplateTableHeader = () => {
    return (
        <Table.Header>
            <Table.Row>
                {HEADER_COLS.map((col, idx) => (
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

export default TemplateTableHeader;
