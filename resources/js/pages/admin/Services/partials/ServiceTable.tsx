import Table from '@/components/ui/Table';
import ServiceCard from '@/components/ui/ServiceCard';
import { formatDateAndTime } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import { edit } from '@/wayfinder/routes/admin/exhibitions/services';
import { App } from '@/wayfinder/types';
import { Link } from '@inertiajs/react';
import { VisuallyHidden } from '@radix-ui/react-visually-hidden';
import { PencilLine } from 'lucide-react';
import { FC } from 'react';

const ServiceTable: FC<
    NodeProps<{
        services: App.Models.Service[] | undefined;
        exhibition: App.Models.Exhibition;
        company: App.Models.Company;
    }>
> = ({ className, services, exhibition, company }) => {
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
                        key="title"
                        width={2}
                    >
                        {service.title}
                    </Table.Cell>
                    <Table.Cell
                        key="deadline"
                        width={1.4}
                    >
                        {formatDateAndTime(new Date(service.deadline))}
                    </Table.Cell>
                    <Table.Cell
                        width={0.5}
                        key="status"
                    >
                        <ServiceCard.Badge
                            className="ml-0"
                            variant={getStatus(service.status)}
                        >
                            {service.status}
                        </ServiceCard.Badge>
                    </Table.Cell>
                    <Table.Cell
                        key="edit-btn"
                        width={0.5}
                    >
                        <Link
                            data-test={`edit-service-${service.id}`}
                            href={edit({
                                service: service.id,
                                exhibition: exhibition,
                                company: company,
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

export default ServiceTable;

function getStatus(status: string) {
    switch (status) {
        case 'Выполнена':
            return 'success';
        case 'На проверке':
            return 'default';
        case 'Просрочена':
            return 'danger';
        default:
            return 'warning';
    }
}
