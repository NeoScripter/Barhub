import { Button } from '@/components/ui/Button';
import CompanyLayout from '@/layouts/app/CompanyLayout';
import { Inertia } from '@/wayfinder/types';
import { Link } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import { FC } from 'react';
import { create } from '@/wayfinder/App/Http/Controllers/Admin/CompanyFollowupController';
import FollowupCard from './partials/FollowupCard';

const Index: FC<Inertia.Pages.Admin.CompanyFollowups.Index> = ({
    followups,
    company,
}) => {
    const CreateLink = () => (
        <Button asChild>
            <Link
                data-test="create-followup"
                href={create({ company })}
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
                {followups.map((followup) => (
                    <FollowupCard
                        key={followup.id}
                        followup={followup}
                        company={company}
                    />
                ))}
            </ul>
        </CompanyLayout>
    );
};

export default Index;
