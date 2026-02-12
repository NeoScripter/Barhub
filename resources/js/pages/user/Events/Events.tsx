import AccentHeading from '@/components/ui/AccentHeading';
import { headingStyles, spacingStyles } from '@/lib/consts/styles';
import { cn } from '@/lib/utils';
import { Inertia } from '@/wayfinder/types';
import { Calendar, MapPin, Rocket } from 'lucide-react';
import { FC } from 'react';
import EventCard from './partials/EventCard';
import EventFilter from './partials/EventFilter';

const Events: FC<Inertia.Pages.User.Events.Events> = ({
    events,
    themes,
    days,
    stages,
}) => {
    return (
        <div className={cn(spacingStyles, 'grid')}>
            <ul className="grid gap-8">
                <EventFilter
                    key="days-filter"
                    icon={Calendar}
                    label="Дни"
                    filters={days}
                />
                <EventFilter
                    key="stages-filter"
                    icon={MapPin}
                    label="Площадки"
                    filters={stages}
                />
                <EventFilter
                    key="themes-filter"
                    icon={Rocket}
                    label="Направления"
                    filters={themes}
                />
            </ul>

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
