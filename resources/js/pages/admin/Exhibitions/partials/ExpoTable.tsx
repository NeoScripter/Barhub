import Badge from '@/components/ui/Badge';
import Table from '@/components/ui/Table';
import { formatDateShort } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import { App } from '@/wayfinder/types';
import { PencilLine } from 'lucide-react';
import { FC } from 'react';

const ExpoTable: FC<
    NodeProps<{ expos: App.Models.Exhibition[]; isSuperAdmin: boolean }>
> = ({ className, expos, isSuperAdmin = true }) => {
    return (
        <Table.Body className={className}>
            {expos.map((expo) => (
                <Table.Row>
                    <Table.Cell width={2}>{expo.name}</Table.Cell>
                    <Table.Cell width={1.2}>
                        {formatDateShort(new Date(expo.starts_at))}
                    </Table.Cell>
                    <Table.Cell width={1.2}>
                        {formatDateShort(new Date(expo.ends_at))}
                    </Table.Cell>
                    <Table.Cell>{expo.location}</Table.Cell>
                    <Table.Cell width={0.5}>
                        <Badge
                            variant={expo.is_active ? 'success' : 'danger'}
                            className="text-xs lg:text-sm"
                        >
                            {expo.is_active ? 'on' : 'off'}
                        </Badge>
                    </Table.Cell>
                    {isSuperAdmin && (
                        <Table.Cell width={0.5}>
                            <button>
                                <PencilLine />
                            </button>
                        </Table.Cell>
                    )}
                </Table.Row>
            ))}
        </Table.Body>
    );
};

export default ExpoTable;
