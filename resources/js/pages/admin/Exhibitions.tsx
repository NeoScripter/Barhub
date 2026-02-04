import Badge from '@/components/ui/Badge';
import Table from '@/components/ui/Table';
import AppLayout from '@/layouts/app/AdminLayout';
import { Inertia } from '@/wayfinder/types';
import { PencilLine } from 'lucide-react';

const HEADER_ROWS = [
    'Название',
    // 'Спикеры',
    'Дата начала',
    'Площадка',
    // 'Направления',
    'Статус',
    'Действия',
];

const Exhibitions = ({ exhibitions }: Inertia.Pages.Admin.Exhibitions) => {
    return (
        <AppLayout>
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
                <Table.Body>
                    {exhibitions.map((expo) => (
                        <Table.Row>
                            <Table.Cell width={2}>{expo.name}</Table.Cell>
                            <Table.Cell>{expo.starts_at}</Table.Cell>
                            <Table.Cell>{expo.location}</Table.Cell>
                            <Table.Cell width={0.5}>
                                <Badge
                                    variant={
                                        expo.is_active ? 'success' : 'danger'
                                    }
                                    className="text-xs lg:text-sm"
                                >
                                    {expo.is_active ? 'on' : 'off'}
                                </Badge>
                            </Table.Cell>
                            <Table.Cell width={0.5}>
                                <button>
                                    <PencilLine />
                                </button>
                            </Table.Cell>
                        </Table.Row>
                    ))}
                </Table.Body>
            </Table>
        </AppLayout>
    );
};

export default Exhibitions;
