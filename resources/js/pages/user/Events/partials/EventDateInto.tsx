import { cn, formatDateShort, formatTime } from '@/lib/utils';
import { App } from '@/wayfinder/types';
import { FC } from 'react';

const EventDateInfo: FC<{ event: App.Models.Event; className?: string }> = ({
    event,
    className,
}) => {
    return (
        <div className={cn('text-primary', className)}>
            <p className="mb-0.5 text-xl font-bold">{`${formatTime(new Date(event.starts_at))}-${formatTime(new Date(event.ends_at))}`}</p>
            <p className="mb-1 text-muted-foreground">
                {formatDateShort(new Date(event.starts_at))}
            </p>
            <p className="font-bold">{event?.stage?.name}</p>
        </div>
    );
};

export default EventDateInfo;
