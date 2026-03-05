import CompanyLayout from '@/layouts/app/CompanyLayout';
import { Inertia } from '@/wayfinder/types';
import { FC } from 'react';

const Index: FC<Inertia.Pages.Admin.Tasks.Index> = ({
    tasks,
}) => {
    return (
        <CompanyLayout className='space-y-30!'>
            hello
        </CompanyLayout>
    );
};

export default Index;
