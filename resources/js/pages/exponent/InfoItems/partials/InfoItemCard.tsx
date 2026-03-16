import CardLayout from '@/components/layout/CardLayout';
import { cn } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import { App } from '@/wayfinder/types';
import { Link } from '@inertiajs/react';
import { Pencil } from 'lucide-react';
import { FC } from 'react';
import {
    edit,
} from '@/wayfinder/routes/admin/info-items';
import Image from '@/components/ui/Image';


const InfoItemCard: FC<NodeProps<{ item: App.Models.InfoItem }>> = ({
    className,
    item,
}) => {

    return (
        <li>
            <CardLayout className={cn('relative w-full max-w-100 mx-auto p-4', className)}>
                <Link
                    data-test={`edit-info-item-${item.id}`}
                    className='absolute top-4 right-4 size-5'
                    href={
                        edit({
                            info_item: item.id,
                        }).url
                    }
                >
                    <Pencil className="text-foreground/50 size-full" />
                </Link>

                {item.image && (
                    <Image image={item.image} wrapperStyles='size-15 my-2 2xl:size-20'  />
                )}
                <div className="flex-1">
                    <p className="font-medium text-foreground 2xl:text-lg mt-1">{item.title}</p>
                </div>
            </CardLayout>
        </li>
    );
};

export default InfoItemCard;
