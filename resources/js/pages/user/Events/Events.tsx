import AccentHeading from '@/components/ui/AccentHeading';
import { headingStyles, spacingStyles } from '@/lib/consts/styles';
import { cn } from '@/lib/utils';
import { Inertia } from '@/wayfinder/types';
import { FC } from 'react';
import EventCard from './partials/EventCard';

const Events: FC<Inertia.Pages.User.Events.Events> = ({ events }) => {
    return (
        <div className={cn(spacingStyles, 'grid')}>
            <div>TODO Filters</div>

            <div>
                <AccentHeading className={headingStyles}>
                    Расписание
                </AccentHeading>

                <ul className="grid gap-4 sm:gap-8">
                    {events.map((event) => (
                        <EventCard
                            key={event.id}
                            event={event}
                        />
                    ))}
                </ul>
            </div>
        </div>
    );
};

export default Events;
