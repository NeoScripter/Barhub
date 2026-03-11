import AccentHeading from '@/components/ui/AccentHeading';
import { Button } from '@/components/ui/Button';
import IndexToolbar from '@/components/ui/IndexToolbar';
import Pagination from '@/components/ui/Pagination';
import {
    create,
    edit,
} from '@/wayfinder/routes/admin/exhibitions/info-items';
import { Inertia } from '@/wayfinder/types';
import { Link } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import { FC } from 'react';

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
                <ul className="flex flex-col gap-4">
                    {infoItems.data.map((item) => (
                        <li
                            key={item.id}
                            className="flex items-center gap-4 rounded border p-4"
                        >
                            {item.image && (
                                <img
                                    src={item.image.url}
                                    alt={item.title}
                                    className="h-12 w-12 rounded object-cover"
                                />
                            )}
                            <div className="flex-1">
                                <p className="font-medium">{item.title}</p>
                                <a
                                    href={item.url}
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    className="text-sm text-primary underline underline-offset-2"
                                >
                                    {item.url}
                                </a>
                            </div>
                            <div className="flex gap-2">
                                <Button
                                    asChild
                                    variant="outline"
                                    size="sm"
                                >
                                    <Link
                                        data-test={`edit-info-item-${item.id}`}
                                        href={
                                            edit({
                                                exhibition,
                                                info_item: item.id,
                                            }).url
                                        }
                                    >
                                        Редактировать
                                    </Link>
                                </Button>
                            </div>
                        </li>
                    ))}
                </ul>
            )}

            <Pagination data={infoItems} />
        </>
    );
};

export default Index;
