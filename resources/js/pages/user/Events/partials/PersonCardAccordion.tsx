import { cn } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import { App } from '@/wayfinder/types';
import { FC } from 'react';
import AccordionLayout from './AccordionLayout';
import EventPersonCard from './EventPersonCard';

const PersonCardAccordion: FC<NodeProps<{ event: App.Models.Event }>> = ({
    event,
}) => {
    const people = event.people;

    return (
        <div className="relative isolate bg-white">
            {people && people.slice(0, 1).map((person) => (
                <EventPersonCard
                    key={person.id}
                    role={person.role}
                    person={person}
                    className="sm:w-53 lg:w-40 lg:shrink-0 2xl:w-53"
                />
            ))}
            <AccordionLayout>
                {people && people.map((person, idx) => (
                    <li key={person.id}>
                        <EventPersonCard
                            role={person.role}
                            person={person}
                            className={cn(
                                'bg-white sm:w-53 lg:w-40 lg:shrink-0 2xl:w-53',
                                idx === 0 && 'hidden sm:flex',
                            )}
                        />
                    </li>
                ))}
            </AccordionLayout>
        </div>
    );
};

export default PersonCardAccordion;
