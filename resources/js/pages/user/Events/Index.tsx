import AccentHeading from '@/components/ui/AccentHeading';
import { Button } from '@/components/ui/Button';
import { Inertia } from '@/wayfinder/types';
import { Download } from 'lucide-react';
import { FC } from 'react';
import EventCard from './partials/EventCard';
import EventFilters from './partials/EventFilters';

const Index: FC<Inertia.Pages.User.Events.Index> = ({ events, exhibition }) => {

    const params = new URLSearchParams(window.location.search);
    const exportUrl = `/exhibitions/${exhibition.id}/export?${params.toString()}`;

    return (
        <div className="spacing grid">
            <EventFilters />

            <div>
                <div className="heading flex items-center justify-between gap-3">
                    <AccentHeading className="text-base">
                        Расписание
                    </AccentHeading>

                    <Button
                        variant="ghost"
                        asChild
                        size="lg"
                    >
                        <a href={exportUrl}>
                            <Download />
                            Скачать список
                        </a>
                    </Button>
                </div>

                {events && events.length > 0 ? (
                    <ul className="grid gap-4 sm:gap-8">
                        {events.map((event, index) => (
                            <EventCard
                                key={event.id}
                                event={event}
                                exhibition={exhibition}
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

export default Index;
