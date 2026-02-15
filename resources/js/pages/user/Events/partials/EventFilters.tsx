import { cn } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import { Inertia } from '@/wayfinder/types';
import { usePage } from '@inertiajs/react';
import { Calendar, MapPin, Rocket } from 'lucide-react';
import { FC } from 'react';
import EventFilter from './EventFilter';

function modifyDate(val: string) {
    return new Intl.DateTimeFormat('ru', {
        day: 'numeric',
        month: 'short',
        timeZone: 'UTC',
    }).format(new Date(val));
}

const EventFilters: FC<NodeProps> = ({ className }) => {
    const { days, stages, themes } =
        usePage<Inertia.Pages.User.Events.Events>().props;
    return (
        <ul className={cn('grid gap-6 2xl:gap-8', className)}>
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
    );
};

export default EventFilters;
