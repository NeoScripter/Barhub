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
            <figure className="size-18 shrink-0 lg:size-12 2xl:size-18">
                <img
                    src={person.avatar}
                    alt={formatFullName(person.name)}
                />
            </figure>

            <div className="space-y-1">
                <p className="font-bold lg:text-sm 2xl:text-base">
                    {formatFullName(person.name)}
                </p>
                <p className="text-sm lg:text-xs 2xl:text-sm">{role}</p>
            </div>
        </div>
    );
};

export default EventPersonCard;

function formatFullName(name: string) {
    const parts = name.split(' ');

    return `${parts[0]} ${parts[2]}`;
}
