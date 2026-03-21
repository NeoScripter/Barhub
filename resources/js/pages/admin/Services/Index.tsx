import AccentHeading from '@/components/ui/AccentHeading';
import { Button } from '@/components/ui/Button';
import IndexToolbar from '@/components/ui/IndexToolbar';
import Pagination from '@/components/ui/Pagination';
import Table from '@/components/ui/Table';
import { create } from '@/wayfinder/routes/admin/services';
import { Inertia } from '@/wayfinder/types';
import { Link } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import { FC } from 'react';
import ServiceTable from './partials/ServiceTable';
import ServiceTableHeader from './partials/ServiceTableHeader';

const Index: FC<Inertia.Pages.Admin.Services.Index> = ({ services }) => {
    return (
        <div>
            <IndexToolbar>
                <div>
                    <AccentHeading className="text-xl mb-2">
                        Работа с партнерами
                    </AccentHeading>
                    <AccentHeading className="text-lg text-secondary 2xl:text-xl">
                        Услуги
                    </AccentHeading>
                </div>
                <Button asChild>
                    <Link
                        data-test="create-service"
                        href={create()}
                    >
                        <Plus />
                        Добавить услугу
                    </Link>
                </Button>
            </IndexToolbar>

            <Table
                isEmpty={services?.data?.length === 0}
                placeholder="По вашему запросу не найдено ни одной услуги"
            >
                <ServiceTableHeader />
                <ServiceTable services={services.data} />
            </Table>
            <Pagination data={services} />
        </div>
    );
};

export default Index;
