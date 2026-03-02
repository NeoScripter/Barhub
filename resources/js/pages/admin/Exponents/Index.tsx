import CompanyLayout from '@/layouts/app/CompanyLayout';
import { Inertia } from '@/wayfinder/types';
import { FC } from 'react';
import ExponentDialog from './partials/ExponentDialog';

const Index: FC<Inertia.Pages.Admin.Exponents.Index> = ({ exponents, users }) => {
    return (
        <CompanyLayout>
            <ul>
                {exponents.map((exponent) => (
                    <li
                        key={exponent.id}
                        className="grid grid-cols-3"
                    >
                        <div>{exponent.name}</div>
                        <div>{exponent.last_login_at}</div>
                    </li>
                ))}
            </ul>

            <ExponentDialog />
        </CompanyLayout>
    );
};

export default Index;
