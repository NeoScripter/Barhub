import CompanyLayout from '@/layouts/app/CompanyLayout';
import { Inertia } from '@/wayfinder/types';
import { FC } from 'react';
import ExponentDialog from './partials/ExponentDialog';
import ExponentRow from './partials/ExponentRow';

const Index: FC<Inertia.Pages.Admin.Exponents.Index> = ({
    exponents,
}) => {
    return (
        <CompanyLayout className='space-y-30!'>
            <ul id='exponent-list' className="mb-8 space-y-14 sm:mb-14 lg:mb-16">
                {exponents.map((exponent) => (
                    <ExponentRow
                        key={exponent.id}
                        user={exponent}
                    />
                ))}
            </ul>

            <ExponentDialog />
        </CompanyLayout>
    );
};

export default Index;
