import CompanyLayout from '@/layouts/app/CompanyLayout';
import { Inertia } from '@/wayfinder/types';
import { FC } from 'react';
import ExponentDialog from './partials/ExponentDialog';
import ExponentRow from './partials/ExponentRow';
import SelectExponent from './partials/SelectExponent';
import CreateExponent from './partials/CreateExponent';

const Index: FC<Inertia.Pages.Admin.Exponents.Index> = ({ exponents }) => {
    return (
        <CompanyLayout className="space-y-30!">
            <ul
                id="exponent-list"
                className="mb-8 space-y-14 sm:mb-14 lg:mb-16"
            >
                {exponents.map((exponent) => (
                    <ExponentRow
                        key={exponent.id}
                        user={exponent}
                    />
                ))}
            </ul>

            <div className='flex items-center gap-4 flex-wrap justify-center'>
            <ExponentDialog
                key="select-exponent"
                label="Выбрать из списка"
            >
                <SelectExponent />
            </ExponentDialog>

            <ExponentDialog
                key="create-exponent"
                label="Создать"
            >
                <CreateExponent />
            </ExponentDialog>
            </div>
        </CompanyLayout>
    );
};

export default Index;
