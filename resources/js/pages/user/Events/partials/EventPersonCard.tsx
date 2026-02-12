import { cn } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import { App } from '@/wayfinder/types';
import { FC } from 'react';

const EventPersonCard: FC<NodeProps<{ person: App.Models.Person }>> = ({
    className,
    person,
}) => {
    return (
        <div className={cn('flex items-start gap-3', className)}>
            <figure className="size-18 shrink-0">
                <img
                    src={person.avatar}
                    alt={formatFullName(person.name)}
                />
            </figure>

            <div className="space-y-1">
                <p className="font-bold">{formatFullName(person.name)}</p>
                <p className="text-sm">организатор</p>
            </div>
        </div>
    );
};

export default EventPersonCard;

function formatFullName(name: string) {
    const parts = name.split(' ');

    return `${parts[0]} ${parts[2]}`;
}
