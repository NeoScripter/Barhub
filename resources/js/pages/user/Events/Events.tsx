import AccentHeading from '@/components/ui/AccentHeading';
import { Inertia } from '@/wayfinder/types';
import { FC } from 'react';
import EventCard from './partials/EventCard';
import EventFilters from './partials/EventFilters';

const Events: FC<Inertia.Pages.User.Events.Events> = ({ events }) => {
    return (
        <div className="spacing grid">
            <EventFilters />

            <div>
                <AccentHeading className="heading text-base">
                    Расписание
                </AccentHeading>

                {events.length > 0 ? (
                    <ul className="grid gap-4 sm:gap-8">
                        {events.map((event, index) => (
                            <EventCard
                                key={event.id}
                                event={event}
                                style={{ zIndex: events.length - index }}
                            />
                        ))}
                    </ul>
                ) : (
                    <p>По вашему запросу не найдено событий</p>
                )}
            </div>
        </div>
    );
};

export default Events;
