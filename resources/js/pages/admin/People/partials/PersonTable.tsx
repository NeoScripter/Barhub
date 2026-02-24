import Image from '@/components/ui/Image';
import Table from '@/components/ui/Table';
import { NodeProps } from '@/types/shared';
import { edit } from '@/wayfinder/routes/admin/exhibitions/people';
import { App } from '@/wayfinder/types';
import { Link } from '@inertiajs/react';
import { PencilLine } from 'lucide-react';
import { FC } from 'react';

const PersonTable: FC<
    NodeProps<{
        people: App.Models.Person[] | undefined;
        exhibition: App.Models.Exhibition;
    }>
> = ({ className, people, exhibition }) => {
    if (!people) {
        return null;
    }

    return (
        <Table.Body className={className}>
            {people.map((person) => (
                <Table.Row key={person.id}>
                    <Table.Cell
                        key="avatar"
                        width={1.2}
                    >
                        {person.avatar && (
                            <Image
                                image={person.avatar}
                                wrapperStyles="size-16"
                            />
                        )}
                    </Table.Cell>
                    <Table.Cell
                        key="name"
                        width={1}
                    >
                        {person.name}
                    </Table.Cell>
                    <Table.Cell
                        key="roles"
                        width={1.5}
                    >
                        <ul className="flex flex-wrap items-baseline gap-2">
                            {person.roles.map((role) => (
                                <li
                                    key={role}
                                    className="rounded-md bg-gray-200 px-2 py-1 text-xs text-foreground"
                                >
                                    {role}
                                </li>
                            ))}
                        </ul>
                    </Table.Cell>
                    <Table.Cell
                        key="telegram"
                        width={0.5}
                        className="wrap-break-word"
                    >
                        {person.telegram}
                    </Table.Cell>
                    <Table.Cell key="stage">{person.events_count}</Table.Cell>
                    <Table.Cell
                        key="edit-btn"
                        width={1}
                    >
                        <Link
                            href={edit({
                                person: person.id,
                                exhibition: exhibition.id,
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

export default PersonTable;
