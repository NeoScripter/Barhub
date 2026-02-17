import Image from '@/components/ui/Image';
import { cn } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import { App } from '@/wayfinder/types';
import { ComponentPropsWithoutRef, FC } from 'react';

const EventPersonCard: FC<
    NodeProps<
        {
            person: App.Models.Person;
            role: string;
        } & ComponentPropsWithoutRef<'div'>
    >
> = ({ className, person, role, ...props }) => {
    return (
        <div
            {...props}
            className={cn('flex items-start gap-3', className)}
        >
            {person.avatar && (
                <Image
                    image={person.avatar}
                    wrapperStyles="size-18 shrink-0 lg:size-12 2xl:size-18"
                />
            )}

            <div className="space-y-1">
                <p className="font-bold lg:text-sm 2xl:text-base">
                    {person.name}
                </p>
                <p className="text-sm lg:text-xs 2xl:text-sm">{role}</p>
            </div>
        </div>
    );
};

export default EventPersonCard;

