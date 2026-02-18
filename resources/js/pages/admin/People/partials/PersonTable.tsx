import Image from '@/components/ui/Image';
import Table from '@/components/ui/Table';
import ThemeBadge from '@/components/ui/ThemeBadge';
import { formatDateAndTime } from '@/lib/utils';
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
                        width={1}
                    >
                        {person.avatar && <Image image={person.avatar} />}
                    </Table.Cell>
                    <Table.Cell
                        key="name"
                        width={1}
                    >
                        {person.name}
                    </Table.Cell>
                    <Table.Cell
                        key="roles"
                        width={1.4}
                    >
                        {formatDateAndTime(new Date(person.starts_at))}
                    </Table.Cell>
                    <Table.Cell key="stage">{person.stage?.name}</Table.Cell>
                    <Table.Cell
                        width={0.5}
                        key="themes"
                    >
                        <ul className="flex flex-wrap items-baseline gap-2">
                            {person.themes?.map((theme) => (
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
                                person: person.id,
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

export default PersonTable;
