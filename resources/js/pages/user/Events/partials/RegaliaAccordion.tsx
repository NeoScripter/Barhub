import { cn } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import { App } from '@/wayfinder/types';
import { FC } from 'react';
import AccordionLayout from './AccordionLayout';

const RegaliaAccordion: FC<NodeProps<{ event: App.Models.Event }>> = ({
    event,
}) => {
    const regaliaCards =
        event.people?.flatMap((person) =>
            person?.roles.map((role) => ({
                key: `${person.id}-${role}`,
                regalia: person.regalia,
            })),
        ) ?? [];

    return (
        <div className="relative isolate max-w-31 shrink-0 bg-white md:mr-10 lg:mr-4 lg:max-w-20 xl:max-w-30 2xl:mr-6">
            {regaliaCards.slice(0, 1).map((card) => (
                <figure className="w-full">
                    <img
                        src={card.regalia}
                        alt="Фото регалии"
                    />
                </figure>
            ))}
            <AccordionLayout>
                {regaliaCards.map((card, idx) => (
                    <li
                        key={card.key}
                        className="max-h-0 transition-[max-height] duration-150 ease-in-out group-hover:max-h-100"
                    >
                        <figure
                            role={card.role}
                            person={card.person}
                            className={cn(
                                'bg-white w-31 lg:w-20 xl:w-30',
                                idx === 0 && 'hidden sm:block',
                            )}
                            style={{ '--offset': idx }}
                        >
                            <img
                                src={card.regalia}
                                alt="Фото регалии"
                            />
                        </figure>
                    </li>
                ))}
            </AccordionLayout>
        </div>
    );
};

export default RegaliaAccordion;
