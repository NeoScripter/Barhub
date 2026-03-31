import CardLayout from '@/components/layout/CardLayout';
import Image from '@/components/ui/Image';
import { cn } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import { App } from '@/wayfinder/types';
import { FC } from 'react';

const InfoItemCard: FC<NodeProps<{ item: App.Models.InfoItem }>> = ({
    className,
    item,
}) => {
    return (
        <li>
            <CardLayout
                className={cn(
                    'relative mx-auto w-full hover:scale-104 transition-transform max-w-100 p-4',
                    className,
                )}
            >
                {item.image && (
                    <Image
                        image={item.image}
                        wrapperStyles="size-15 my-2 2xl:size-20"
                    />
                )}
                <a
                    href={item.url}
                    target="_blank"
                    className='absolute inset-0'
                >
                </a>
                <div className="flex-1">
                    <p className="mt-1 font-medium text-foreground transition-colors 2xl:text-lg">
                        {item.title}
                    </p>
                </div>
            </CardLayout>
        </li>
    );
};

export default InfoItemCard;
