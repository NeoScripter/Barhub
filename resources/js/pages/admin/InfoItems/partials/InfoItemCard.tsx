import CardLayout from '@/components/layout/CardLayout';
import Image from '@/components/ui/Image';
import { cn } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import { edit } from '@/wayfinder/routes/admin/info-items';
import { App } from '@/wayfinder/types';
import { Link } from '@inertiajs/react';
import { Pencil } from 'lucide-react';
import { FC } from 'react';

const InfoItemCard: FC<NodeProps<{ item: App.Models.InfoItem }>> = ({
    className,
    item,
}) => {
    return (
        <li>
            <CardLayout
                className={cn(
                    'relative mx-auto w-full max-w-100 p-4',
                    className,
                )}
            >
                <Link
                    data-test={`edit-info-item-${item.id}`}
                    className="absolute top-4 right-4 size-5"
                    href={
                        edit({
                            info_item: item.id,
                        }).url
                    }
                >
                    <Pencil className="size-full text-foreground/50" />
                </Link>

                {item.image && (
                    <Image
                        image={item.image}
                        imgStyles="object-contain"
                        wrapperStyles="size-15 my-2 2xl:size-20"
                    />
                )}
                <div className="flex-1">
                    <a
                        href={item.url}
                        target="_blank"
                        className="mt-1 font-medium text-foreground underline underline-offset-3 transition-colors hover:text-primary focus:text-primary 2xl:text-lg"
                    >
                        {item.title}
                    </a>
                </div>
            </CardLayout>
        </li>
    );
};

export default InfoItemCard;
