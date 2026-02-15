import AccentHeading from '@/components/ui/AccentHeading';
import { Inertia } from '@/wayfinder/types';
import { Calendar, MapPin, Rocket } from 'lucide-react';
import { FC } from 'react';
import EventCard from './partials/EventCard';
import EventFilter from './partials/EventFilter';

export function modifyDate(val: string) {
    return new Intl.DateTimeFormat('ru', {
        day: 'numeric',
        month: 'short',
        timeZone: 'UTC',
    }).format(new Date(val));
}

const Events: FC<Inertia.Pages.User.Events.Events> = ({
    events,
    themes,
    days,
    stages,
}) => {
    return (
        <div className="spacing grid">
            <ul className="grid gap-6 2xl:gap-8">
                <EventFilter
                    key="days-filter"
                    icon={Calendar}
                    label="Дни"
                    filterKey="starts_at"
                    filters={days}
                    modifier={modifyDate}
                />
                <EventFilter
                    key="stages-filter"
                    icon={MapPin}
                    label="Площадки"
                    filterKey="stage.name"
                    filters={stages}
                />
                <EventFilter
                    key="themes-filter"
                    icon={Rocket}
                    label="Направления"
                    filterKey="themes.name"
                    filters={themes}
                />
            </ul>

            <div>
                <AccentHeading className="heading text-base">
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
