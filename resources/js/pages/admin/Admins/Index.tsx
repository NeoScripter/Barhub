import { Inertia } from '@/wayfinder/types';
import { FC } from 'react';
import AdminDialog from './partials/AdminDialog';
import AdminRow from './partials/AdminRow';
import IndexToolbar from '@/components/ui/IndexToolbar';
import AccentHeading from '@/components/ui/AccentHeading';

const Index: FC<Inertia.Pages.Admin.Admins.Index> = ({ admins }) => {
    return (
        <div>
            <IndexToolbar className="items-center md:flex-col md:items-start">
                <AccentHeading className="text-secondary text-xl">
                    Управление администраторами
                </AccentHeading>
            </IndexToolbar>

            <ul
                id="admin-list"
                className="my-8 space-y-14 sm:my-14 lg:my-16"
            >
                {admins.map((admin) => (
                    <AdminRow
                        key={admin.id}
                        user={admin}
                    />
                ))}
            </ul>

            <AdminDialog />
        </div>
    );
};

export default Index;
