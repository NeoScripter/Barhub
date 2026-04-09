import Badge from '@/components/ui/Badge';
import Image from '@/components/ui/Image';
import Table from '@/components/ui/Table';
import { range } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import { edit } from '@/wayfinder/routes/admin/companies';
import { App } from '@/wayfinder/types';
import { Link } from '@inertiajs/react';
import { FC } from 'react';
import TaskStatusLine from './TaskStatusLine';

const CompanyTable: FC<
    NodeProps<{
        companies: App.Models.Company[] | undefined;
    }>
> = ({ className, companies }) => {
    if (!companies) {
        return null;
    }

    return (
        <Table.Body className={className}>
            {companies.map((company) => (
                <Table.Row key={company.id}>
                    <Table.Cell
                        key="logo"
                        width={1.2}
                    >
                        <Link
                            href={edit({
                                company: company.id,
                            })}
                            className="absolute inset-0"
                        />

                        {company.logo && (
                            <Image
                                image={company.logo}
                                wrapperStyles="size-16"
                                imgStyles="object-contain"
                            />
                        )}
                    </Table.Cell>
                    <Table.Cell
                        key="public_name"
                        width={1}
                    >
                        {company.public_name}
                    </Table.Cell>
                    <Table.Cell
                        key="legal_name"
                        width={1}
                    >
                        {company.legal_name}
                    </Table.Cell>
                    <Table.Cell
                        key="stand_code"
                        width={1}
                    >
                        {company.stand_code}
                    </Table.Cell>
                    <Table.Cell
                        key="tags"
                        width={1}
                    >
                        <ul className="flex flex-wrap items-baseline gap-2">
                            {company.tags.map((tag) => (
                                <li
                                    key={tag.id}
                                    className="rounded-md bg-gray-200 px-2 py-1 text-xs text-foreground"
                                >
                                    {tag.name}
                                </li>
                            ))}
                        </ul>
                    </Table.Cell>
                    <Table.Cell
                        width={0.5}
                        key="status"
                    >
                        <Badge
                            className="ml-0"
                            variant={
                                company.show_on_site ? 'success' : 'danger'
                            }
                        >
                            {company.show_on_site ? 'ON' : 'OFF'}
                        </Badge>
                    </Table.Cell>

                    <Table.Cell
                        key="tasks"
                        width={2}
                    >
                        {range(1, 5).map((num) => (
                            <TaskStatusLine
                                key={num}
                                tasks={company.tasks}
                                status={num}
                            />
                        ))}
                    </Table.Cell>
                    <Table.Cell
                        key="services"
                        width={1.5}
                    >
                        <span className="text-sm">
                            Услуги: <strong>{company.followups_count}</strong>
                        </span>
                    </Table.Cell>
                </Table.Row>
            ))}
        </Table.Body>
    );
};

export default CompanyTable;
