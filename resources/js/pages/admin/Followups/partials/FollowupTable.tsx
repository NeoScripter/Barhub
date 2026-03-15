import Table from '@/components/ui/Table';
import { NodeProps } from '@/types/shared';
import { edit } from '@/wayfinder/routes/admin/followups';
import { App } from '@/wayfinder/types';
import { Link } from '@inertiajs/react';
import { VisuallyHidden } from '@radix-ui/react-visually-hidden';
import { PencilLine } from 'lucide-react';
import { FC } from 'react';

const FollowupTable: FC<
    NodeProps<{
        followups: App.Models.Followup[] | undefined;
        exhibition: App.Models.Exhibition;
    }>
> = ({ className, followups, exhibition }) => {
    if (!followups) {
        return null;
    }

    return (
        <Table.Body
            id="followups-table"
            className={className}
        >
            {followups.map((followup) => (
                <Table.Row key={followup.id}>
                    <Table.Cell
                        key="name"
                        width={1}
                    >
                        {followup.service?.name}
                    </Table.Cell>
                    <Table.Cell
                        key="description"
                        width={2}
                        className='max-w-80'
                    >
                        <p>{followup.service?.description}</p>
                    </Table.Cell>
                    <Table.Cell
                        key="edit-btn"
                        width={0.5}
                    >
                        <Link
                            data-test={`edit-followup-${followup.id}`}
                            href={edit({
                                followup: followup.id,
                                exhibition: exhibition,
                            })}
                        >
                            <VisuallyHidden>Принять услугу</VisuallyHidden>
                            <PencilLine />
                        </Link>
                    </Table.Cell>
                </Table.Row>
            ))}
        </Table.Body>
    );
};

export default FollowupTable;
