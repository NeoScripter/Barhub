import Table from '@/components/ui/Table';
import { formatDateAndTime } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import { edit } from '@/wayfinder/routes/admin/task-templates';
import { App } from '@/wayfinder/types';
import { Link } from '@inertiajs/react';
import { VisuallyHidden } from '@radix-ui/react-visually-hidden';
import { PencilLine } from 'lucide-react';
import { FC } from 'react';

const TemplateTable: FC<
    NodeProps<{
        templates: App.Models.TaskTemplate[] | undefined;
    }>
> = ({ className, templates }) => {
    if (!templates) {
        return null;
    }

    return (
        <Table.Body
            id="templates-table"
            className={className}
        >
            {templates.map((template) => (
                <Table.Row key={template.id}>
                    <Table.Cell
                        key="title"
                        width={2}
                    >
                        {template.title}
                    </Table.Cell>
                    <Table.Cell
                        key="deadline"
                        width={1.4}
                    >
                        {formatDateAndTime(new Date(template.deadline))}
                    </Table.Cell>
                    <Table.Cell
                        key="edit-btn"
                        width={0.5}
                    >
                        <Link
                            data-test={`edit-task-${template.id}`}
                            href={edit({
                                task_template: template.id,
                            })}
                        >
                            <VisuallyHidden>
                                Редактировать задачу
                            </VisuallyHidden>
                            <PencilLine />
                        </Link>
                    </Table.Cell>
                </Table.Row>
            ))}
        </Table.Body>
    );
};

export default TemplateTable;
