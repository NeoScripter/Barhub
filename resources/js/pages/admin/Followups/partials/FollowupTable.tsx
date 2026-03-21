import Table from '@/components/ui/Table';
import TaskCard from '@/components/ui/TaskCard';
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
    }>
> = ({ className, followups }) => {
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
                        key="company"
                        width={1}
                    >
                        {followup.company?.public_name}
                    </Table.Cell>
                    <Table.Cell
                        key="name"
                        width={2}
                        className="max-w-80"
                    >
                        <p>{followup.name}</p>
                    </Table.Cell>
                    <Table.Cell
                        width={0.5}
                        key="status"
                    >
                        <TaskCard.Badge
                            className="ml-0"
                            variant='success'
                        >
                            заявка на услуги
                        </TaskCard.Badge>
                    </Table.Cell>

                    <Table.Cell
                        key="edit-btn"
                        width={0.5}
                    >
                        <Link
                            data-test={`edit-followup-${followup.id}`}
                            href={edit({
                                followup: followup.id,
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
