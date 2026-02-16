import { cn } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import { App } from '@/wayfinder/types';
import { FC } from 'react';
import AccordionLayout from './AccordionLayout';
import EventPersonCard from './EventPersonCard';

const PersonCardAccordion: FC<NodeProps<{ event: App.Models.Event }>> = ({
    event,
}) => {
    // const personCards =
    //     event.people?.flatMap((person) =>
    //         person?.roles.map((role) => ({
    //             key: `${person.id}-${role}`,
    //             role,
    //             person,
    //         })),
    //     ) ?? [];
    const personCards =
        event.people?.map((person) => ({
            key: `${person.id}`,
            person,
        })) ?? [];

    console.log(event.people)

    return (
        <div className="relative isolate bg-white">
            {personCards.slice(0, 1).map((card) => (
                <EventPersonCard
                    key={card.key}
                    role={'role'}
                    person={card.person}
                    className="sm:w-53 lg:w-40 lg:shrink-0 2xl:w-53"
                />
            ))}
            <AccordionLayout>
                {personCards.map((card, idx) => (
                    <li key={card.key}>
                        <EventPersonCard
                            role={'role'}
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
