import { NodeProps } from '@/types/shared';
import { App } from '@/wayfinder/types';
import { FC } from 'react';
import EventPersonCard from './EventPersonCard';
import AccordionLayout from './AccordionLayout';
import { cn } from '@/lib/utils';

const PersonCardAccordion: FC<NodeProps<{ event: App.Models.Event }>> = ({
    event,
}) => {
    const personCards =
        event.people?.flatMap((person) =>
            person?.roles.map((role) => ({
                key: `${person.id}-${role}`,
                role,
                person,
            })),
        ) ?? [];

    return (
        <div className="relative isolate bg-white">
            {personCards.slice(0, 1).map((card) => (
                <EventPersonCard
                    key={card.key}
                    role={card.role}
                    person={card.person}
                    className="sm:w-53 lg:w-40 lg:shrink-0 2xl:w-53"
                />
            ))}
            <AccordionLayout>
                {personCards.map((card, idx) => (
                    <li
                        key={card.key}
                    >
                        <EventPersonCard
                            role={card.role}
                            person={card.person}
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
