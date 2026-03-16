import AccentHeading from '@/components/ui/AccentHeading';
import IndexToolbar from '@/components/ui/IndexToolbar';
import Pagination from '@/components/ui/Pagination';
import { Inertia } from '@/wayfinder/types';
import { FC } from 'react';
import InfoItemCard from './partials/InfoItemCard';

const Index: FC<Inertia.Pages.Admin.InfoItems.Index> = ({ infoItems }) => {
    return (
        <>
            <IndexToolbar>
                <AccentHeading className="mb-2 text-xl sm:mb-3 xl:mb-5">
                    Информация и материалы
                </AccentHeading>
            </IndexToolbar>

            {infoItems?.length === 0 ? (
                <p className="text-sm text-muted-foreground">
                    Информация и материалы не найдены
                </p>
            ) : (
                <ul className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    {infoItems.map((item) => (
                        <InfoItemCard
                            key={item.id}
                            item={item}
                        />
                    ))}
                </ul>
            )}

            <Pagination data={infoItems} />
        </>
    );
};

export default Index;
