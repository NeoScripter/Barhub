import CardLayout from '@/components/layout/CardLayout';
import AccentHeading from '@/components/ui/AccentHeading';
import ThemeBadge from '@/components/ui/ThemeBadge';
import { formatDateShort, formatTime } from '@/lib/utils';
import InfoItem from '@/pages/exponent/Companies/partials/InfoItem';
import { App } from '@/wayfinder/types';
import { Calendar, MapPin } from 'lucide-react';
import { FC } from 'react';
import PersonCard from './partials/PersonCard';

const Show: FC<{
    event: App.Models.Event;
    exhibition: App.Models.Exhibition;
}> = ({ event, exhibition }) => {
    return (
        <div className="space-y-10 sm:space-y-14 xl:space-y-18">
            <CardLayout className="w-full items-start gap-5 px-7 py-9 lg:gap-8 lg:px-10 lg:py-11">
                <AccentHeading
                    asChild
                    className="text-xl lg:text-2xl"
                >
                    <h1>{event.title}</h1>
                </AccentHeading>

                <ul className="grid gap-4 lg:gap-6">
                    {event.starts_at && event.ends_at && (
                        <InfoItem>
                            <Calendar />
                            <span className="text-foreground">
                                {`${formatDateShort(new Date(event.starts_at))}, ${formatTime(new Date(event.starts_at))}-${formatTime(new Date(event.ends_at))}`}
                            </span>
                        </InfoItem>
                    )}
                    {event.stage && (
                        <InfoItem>
                            <MapPin />
                            <span className="text-foreground">
                                {event.stage?.name}
                            </span>
                        </InfoItem>
                    )}
                </ul>

                <p className="text-foreground lg:text-lg">
                    {event.description}
                </p>
            </CardLayout>

            {event.people && (
                <ul className="mx-auto grid max-w-90 gap-8 sm:max-w-full sm:grid-cols-[repeat(auto-fill,minmax(28rem,1fr))]">
                    {event.people.map((person) => (
                        <PersonCard
                            key={person.id}
                            person={person}
                            exhibition={exhibition}
                            className="rounded-lg px-5 py-3 text-base"
                        />
                    ))}
                </ul>
            )}
            {event.themes && (
                <ul className="flex flex-wrap items-baseline gap-4">
                    {event.themes.map((theme) => (
                        <ThemeBadge
                            key={theme.name}
                            theme={theme}
                            className="rounded-lg px-5 py-3 text-base"
                        />
                    ))}
                </ul>
            )}
        </div>
    );
};

export default Show;
