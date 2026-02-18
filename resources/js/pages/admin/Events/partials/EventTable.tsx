import Table from '@/components/ui/Table';
import ThemeBadge from '@/components/ui/ThemeBadge';
import { formatDateAndTime, formatDateShort } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import { edit } from '@/wayfinder/routes/admin/exhibitions/events';
import { App } from '@/wayfinder/types';
import { Link } from '@inertiajs/react';
import { PencilLine } from 'lucide-react';
import { FC } from 'react';

const EventTable: FC<
    NodeProps<{
        events: App.Models.Event[] | undefined;
        exhibition: App.Models.Exhibition;
    }>
> = ({ className, events, exhibition }) => {

    if (!events) {
        return null;
    }

    return (
        <Table.Body className={className}>
            {events.map((event) => (
                <Table.Row key={event.id}>
                    <Table.Cell
                        key="name"
                        width={2}
                    >
                        {event.title}
                    </Table.Cell>
                    <Table.Cell
                        key="people"
                        width={0.75}
                    >
                        {event.people?.map((person) => person.name).join(', ')}
                    </Table.Cell>
                    <Table.Cell
                        key="startDate"
                        width={1.4}
                    >
                        {formatDateAndTime(new Date(event.starts_at))}
                    </Table.Cell>
                    <Table.Cell key="stage">{event.stage?.name}</Table.Cell>
                    <Table.Cell
                        width={0.5}
                        key="themes"
                    >
                        <ul className="flex flex-wrap items-baseline gap-2">
                            {event.themes?.map((theme) => (
                                <ThemeBadge
                                    key={theme.name}
                                    theme={theme}
                                />
                            ))}
                        </ul>
                    </Table.Cell>
                    <Table.Cell
                        key="edit-btn"
                        width={0.5}
                    >
                        <Link
                            href={edit({
                                event: event.id,
                                exhibition: exhibition.slug,
                            })}
                        >
                            <PencilLine />
                        </Link>
                    </Table.Cell>
                </Table.Row>
            ))}
        </Table.Body>
    );
};

export default EventTable;
