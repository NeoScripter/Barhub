import { Button } from '@/components/ui/Button';
import Pagination from '@/components/ui/Pagination';
import Table from '@/components/ui/Table';
import CompanyLayout from '@/layouts/app/CompanyLayout';
import { create } from '@/wayfinder/routes/admin/exhibitions/services';
import { Inertia } from '@/wayfinder/types';
import { Link } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import { FC } from 'react';

const Index: FC<Inertia.Pages.Admin.Services.Index> = ({
    services,
    exhibition,
    company,
}) => {
    const CreateLink = () => (
        <Button asChild>
            <Link
                data-test="create-service"
                href={create({ exhibition, company })}
            >
                <Plus />
                Создать задачу
            </Link>
        </Button>
    );
    return (
        <CompanyLayout
            className="space-y-30!"
            createLink={CreateLink}
        >

            <Pagination data={services} />
        </CompanyLayout>
    );
};

export default Index;
