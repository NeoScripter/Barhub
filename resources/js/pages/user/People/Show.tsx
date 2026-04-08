import AccentHeading from '@/components/ui/AccentHeading';
import Image from '@/components/ui/Image';
import { Send } from 'lucide-react';
import { FC } from 'react';

import InfoItem from '@/pages/exponent/Companies/partials/InfoItem';
import { App } from '@/wayfinder/types';
import EventCard from './partials/EventCard';

const Show: FC<{
    person: App.Models.Person;
    exhibition: App.Models.Exhibition;
}> = ({ person, exhibition }) => {
    return (
        <div className="space-y-12 md:space-y-14 2xl:space-y-16">
            <div className="flex flex-wrap items-start justify-between gap-4 sm:flex-row sm:gap-9 xl:items-center">
                {person.avatar && (
                    <Image
                        wrapperStyles="max-w-24 sm:max-w-50 shrink-0 xl:max-w-62"
                        imgStyles="object-contain"
                        image={person.avatar}
                    />
                )}
                {person.logo && (
                    <Image
                        wrapperStyles="max-w-44 xl:order-2 sm:max-w-51.5 xl:max-w-63.5"
                        imgStyles="object-contain"
                        image={person.logo}
                    />
                )}
                <div className="max-w-140 xl:mr-auto">
                    <AccentHeading
                        asChild
                        className="mb-1 text-xl lg:text-2xl"
                    >
                        <h1>{person.name}</h1>
                    </AccentHeading>

                    <p className="text-balance">{`Регалии: ${person.regalia}`}</p>
                </div>
            </div>

            <p className="lg:text-lg">{person.bio}</p>

            <ul className="flex flex-col flex-wrap items-baseline gap-x-10 gap-y-6 md:flex-row">
                {person.telegram && (
                    <InfoItem
                        href={`https://t.me/${person.telegram.replaceAll('@', '')}`}
                    >
                        <Send />
                        {person.telegram}
                    </InfoItem>
                )}
            </ul>

            {person.events && person.events.length > 0 && (
                <div>
                    <AccentHeading
                        asChild
                        className="mb-4 text-xl lg:mb-6 lg:text-2xl"
                    >
                        <h2>Лекции:</h2>
                    </AccentHeading>
                </div>
            )}

            <ul className="grid items-stretch gap-5 sm:grid-cols-[repeat(auto-fill,minmax(20rem,1fr))]">
                {person.events?.map((event) => (
                    <EventCard
                        key={event.id}
                        event={event}
                        exhibition={exhibition}
                    />
                ))}
            </ul>
        </div>
    );
};

export default Show;
