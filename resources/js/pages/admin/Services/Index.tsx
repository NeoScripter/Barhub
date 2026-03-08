import { Button } from '@/components/ui/Button';
import Pagination from '@/components/ui/Pagination';
import CompanyLayout from '@/layouts/app/CompanyLayout';
import { create } from '@/wayfinder/routes/admin/exhibitions/services';
import { Inertia } from '@/wayfinder/types';
import { Link } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import { FC } from 'react';
import ServiceCard from './partials/ServiceCard';

const Index: FC<Inertia.Pages.Admin.Services.Index> = ({
    services,
    exhibition,
    company,
}) => {

    console.log(services)
    const CreateLink = () => (
        <Button asChild>
            <Link
                data-test="create-service"
                href={create({ exhibition, company })}
            >
                Добавить услугу
                <Plus />
            </Link>
        </Button>
    );
    return (
        <CompanyLayout
            className="space-y-30!"
            createLink={CreateLink}
        >
            <ul className="grid sm:grid-cols-[repeat(auto-fit,minmax(20rem,1fr))] xl:grid-cols-3 gap-6 lg:gap-7 xl:gap-8">
                {services.data.map((service) => (
                    <ServiceCard
                        service={service}
                        key={service.id}
                    />
                ))}
            </ul>

            <Pagination data={services} />
        </CompanyLayout>
    );
};

export default Index;
