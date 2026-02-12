import CardLayout from '@/components/layout/CardLayout';
import { paddingStyles } from '@/lib/consts/styles';
import { cn, shortenDescription } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import { App } from '@/wayfinder/types';
import { FC } from 'react';
import EventDateInfo from './EventDateInto';
import EventPersonCard from './EventPersonCard';

const EventCard: FC<NodeProps<{ event: App.Models.Event }>> = ({
    className,
    event,
}) => {
    return (
        <li>
            <CardLayout
                className={cn(
                    className,
                    paddingStyles,
                    'w-full items-start gap-6 text-foreground sm:items-center sm:gap-9 lg:flex-row',
                )}
            >
                <div className="flex flex-col items-start gap-6 sm:w-full sm:flex-row sm:justify-between lg:gap-10">
                    <EventDateInfo
                        event={event}
                        className="sm:w-53 lg:w-35"
                    />

                    <p className="text-sm font-bold sm:shrink-100 sm:text-base">
                        {shortenDescription(event.description)}
                    </p>
                </div>

                <div className="flex flex-col items-start gap-6 sm:w-full sm:flex-row lg:gap-10">
                    <EventPersonCard
                        person={event.organizer}
                        className="sm:w-53 lg:w-60 lg:shrink-0"
                    />

                    <figure className="max-w-31 md:mr-10 lg:hidden 2xl:mr-0 2xl:block">
                        <img
                            src={event.organizer?.regalia}
                            alt="Фото регалии"
                        />
                    </figure>

                    <ul className="flex flex-wrap items-baseline gap-2">
                        {event.themes?.map((theme) => (
                            <li
                                key={theme.name}
                                className="rounded-full px-2 py-1 text-xs"
                                style={{ backgroundColor: theme.color_hex }}
                            >
                                {theme.name}
                            </li>
                        ))}
                    </ul>
                </div>
            </CardLayout>
        </li>
    );
};

export default EventCard;
