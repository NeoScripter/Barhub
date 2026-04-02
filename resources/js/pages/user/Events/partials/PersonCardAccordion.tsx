import { cn } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import { App } from '@/wayfinder/types';
import { FC } from 'react';
import EventPersonCard from './EventPersonCard';

const PersonCardAccordion: FC<NodeProps<{ event: App.Models.Event }>> = ({
    event,
}) => {
    const people = event.people;

    return (
        <div className="relative isolate bg-white">
            <ul className="space-y-5">
                {people &&
                    people.map((person, idx) => (
                        <li key={`${person.id} ${idx}`}>
                            <EventPersonCard
                                role={person.role}
                                person={person}
                                className={cn(
                                    'bg-white sm:w-53 lg:w-40 lg:shrink-0 2xl:w-53',
                                )}
                            />
                        </li>
                    ))}
            </ul>
        </div>
    );
};

export default PersonCardAccordion;
