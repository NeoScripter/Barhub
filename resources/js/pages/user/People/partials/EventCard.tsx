import CardLayout from '@/components/layout/CardLayout';
import { cn, shortenDescription } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import { show } from '@/wayfinder/routes/events';
import { App } from '@/wayfinder/types';
import { Link } from '@inertiajs/react';
import { FC } from 'react';
import EventDateInfo from '../../Events/partials/EventDateInto';

const EventCard: FC<
    NodeProps<{ event: App.Models.Event; exhibition: App.Models.Exhibition }>
> = ({ className, exhibition, event }) => {
    return (
        <li>
            <CardLayout
                className={cn(
                    'relative size-full items-start justify-start gap-3 px-8 py-7 text-foreground transition-transform duration-150 ease-in-out hover:scale-103 hover:ring-2 hover:ring-primary',
                    className,
                )}
            >
                <Link
                    href={show({ exhibition, event })}
                    className="absolute inset-0"
                />
                <p className="font-bold">
                    {shortenDescription(event.description, 20)}
                </p>
                <p className="">{event?.role_label}</p>
                <div>
                    <EventDateInfo
                        event={event}
                        className="sm:w-53 lg:w-30 2xl:w-40"
                    />
                    <h4 className="text-lg font-bold text-primary">
                        asd ads dsad{event.title}
                    </h4>
                </div>
            </CardLayout>
        </li>
    );
};

export default EventCard;
