import AccentHeading from '@/components/ui/AccentHeading';
import { Button } from '@/components/ui/Button';
import IndexToolbar from '@/components/ui/IndexToolbar';
import Pagination from '@/components/ui/Pagination';
import { create } from '@/wayfinder/routes/admin/info-items';
import { Inertia } from '@/wayfinder/types';
import { Link } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import { FC } from 'react';
import InfoItemCard from './partials/InfoItemCard';

const Index: FC<Inertia.Pages.Admin.InfoItems.Index> = ({
    infoItems,
    exhibition,
}) => {
    return (
        <>
            <IndexToolbar>
                <AccentHeading className="mb-2 text-xl sm:mb-3 xl:mb-5">
                    Информационные элементы
                </AccentHeading>
                <Button asChild>
                    <Link
                        data-test="create-info-item"
                        href={create({ exhibition }).url}
                    >
                        <Plus />
                        Добавить элемент
                    </Link>
                </Button>
            </IndexToolbar>

            {infoItems?.data?.length === 0 ? (
                <p className="text-sm text-muted-foreground">
                    Информационные элементы не найдены
                </p>
            ) : (
                <ul className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    {infoItems.data.map((item) => (
                        <InfoItemCard
                            key={item.id}
                            item={item}
                            exhibition={exhibition}
                        />
                    ))}
                </ul>
            )}

            <Pagination data={infoItems} />
        </>
    );
};

export default Index;
