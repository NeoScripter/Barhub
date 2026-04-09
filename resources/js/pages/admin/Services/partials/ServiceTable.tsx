import Table from '@/components/ui/Table';
import { NodeProps } from '@/types/shared';
import { edit } from '@/wayfinder/routes/admin/services';
import { App } from '@/wayfinder/types';
import { Link } from '@inertiajs/react';
import { VisuallyHidden } from '@radix-ui/react-visually-hidden';
import { PencilLine } from 'lucide-react';
import { FC } from 'react';

const ServiceTable: FC<
    NodeProps<{
        services: App.Models.Service[] | undefined;
    }>
> = ({ className, services }) => {
    if (!services) {
        return null;
    }

    return (
        <Table.Body
            id="services-table"
            className={className}
        >
            {services.map((service) => (
                <Table.Row key={service.id}>
                    <Table.Cell
                        key="name"
                        width={1}
                    >
                        <Link
                            data-test={`edit-service-${service.id}`}
                            href={edit({
                                service: service.id,
                            })}
                            className="absolute inset-0"
                        />
                        {service.name}
                    </Table.Cell>
                    <Table.Cell
                        key="description"
                        width={2}
                        className='max-w-80'
                    >
                        <p>{service.description}</p>
                    </Table.Cell>
                </Table.Row>
            ))}
        </Table.Body>
    );
};

export default ServiceTable;
