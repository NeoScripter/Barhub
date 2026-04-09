import Table from '@/components/ui/Table';
import { getSortUrl } from '@/lib/utils';
import { Link } from '@inertiajs/react';
import { ChevronsUpDown } from 'lucide-react';

type Column = { label: string; query?: string };

const HEADER_COLS: Column[] = [
    { label: 'Лого' },
    { label: 'Название', query: 'public_name' },
    { label: 'Юр. лицо'},
    { label: 'Код стенда'},
    { label: 'Тэги', query: 'tags.name' },
    { label: 'Статус публикации'},
    { label: 'Статусы задач'},
    { label: 'Услуги' },
] as const;

const CompanyTableHeader = () => {
    return (
        <Table.Header>
            <Table.Row className='hover:bg-transparent'>
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

export default CompanyTableHeader;
