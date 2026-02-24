import Badge from '@/components/ui/Badge';
import Table from '@/components/ui/Table';
import { formatDateShort } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import { edit, show } from '@/wayfinder/routes/admin/exhibitions';
import { App } from '@/wayfinder/types';
import { Link } from '@inertiajs/react';
import { PencilLine } from 'lucide-react';
import { FC } from 'react';

const ExpoTable: FC<
    NodeProps<{
        expos: App.Models.Exhibition[] | undefined;
        isSuperAdmin: boolean;
    }>
> = ({ className, expos, isSuperAdmin = true }) => {
    if (!expos) {
        return null;
    }

    return (
        <Table.Body className={className}>
            {expos.map((expo) => (
                <Table.Row key={expo.id}>
                    <Table.Cell
                        key="name"
                        width={2}
                        className="relative"
                    >
                        <Link
                            href={show({ id: expo.id })}
                            className="absolute inset-0 block"
                        />
                        {expo.name}
                    </Table.Cell>
                    <Table.Cell
                        key="startDate"
                        width={1.4}
                    >
                        {formatDateShort(new Date(expo.starts_at))}
                    </Table.Cell>
                    <Table.Cell
                        key="endDate"
                        width={1.4}
                    >
                        {formatDateShort(new Date(expo.ends_at))}
                    </Table.Cell>
                    <Table.Cell key="location">{expo.location}</Table.Cell>
                    <Table.Cell
                        width={0.5}
                        key="badge"
                    >
                        <Badge
                            variant={expo.is_active ? 'success' : 'danger'}
                            className="text-xs lg:text-sm"
                        >
                            {expo.is_active ? 'on' : 'off'}
                        </Badge>
                    </Table.Cell>
                    {isSuperAdmin && (
                        <Table.Cell
                            key="edit-btn"
                            width={0.5}
                        >
                            <Link
                                href={edit({
                                    exhibition: expo.id,
                                })}
                                data-test={`edit-expo-${expo.id}`}
                            >
                                <PencilLine />
                            </Link>
                        </Table.Cell>
                    )}
                </Table.Row>
            ))}
        </Table.Body>
    );
};

export default ExpoTable;
