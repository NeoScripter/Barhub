import CardLayout from '@/components/layout/CardLayout';
import { paddingStyles } from '@/lib/consts/styles';
import { cn, shortenDescription } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import { App } from '@/wayfinder/types';
import { ComponentProps, FC } from 'react';
import EventDateInfo from './EventDateInto';
import EventPersonCard from './EventPersonCard';

const EventCard: FC<
    NodeProps<{ event: App.Models.Event } & ComponentProps<'li'>>
> = ({ className, event, ...props }) => {
    const personCards =
        event.people?.flatMap((person) =>
            person?.roles.map((role) => ({
                key: `${person.id}-${role}`,
                role,
                person,
            })),
        ) ?? [];
    return (
        <li
            {...props}
            className="isolate"
        >
            <CardLayout
                className={cn(
                    className,
                    paddingStyles,
                    'group w-full items-start gap-6 slide-down-parent text-foreground transition-transform duration-150 ease-in-out hover:scale-105 sm:items-center sm:gap-9 lg:flex-row',
                )}
            >
                <div className="flex flex-col items-start gap-6 sm:w-full sm:flex-row sm:justify-between lg:gap-10">
                    <EventDateInfo
                        event={event}
                        className="sm:w-53 lg:w-30 2xl:w-40"
                    />

                    <p className="text-sm font-bold sm:shrink-100 sm:text-base lg:text-sm 2xl:text-base">
                        {shortenDescription(event.description)}
                    </p>
                </div>

                <div className="flex flex-col items-start gap-6 sm:w-full sm:flex-row lg:gap-2 xl:gap-6 2xl:gap-8">
                    <div className="relative isolate bg-white">
                        {personCards.slice(0, 1).map((card) => (
                            <EventPersonCard
                                key={card.key}
                                role={card.role}
                                person={card.person}
                                className="sm:w-53 lg:w-40 lg:shrink-0 2xl:w-53"
                            />
                        ))}
                        <div
                            className={cn(
                                'absolute top-0 bg-white opacity-100 z-20 shadow-sm transition-opacity duration-250 ease-in-out group-hover:z-20 group-hover:opacity-100',
                            )}
                        >
                            <ul className="grid bg-white">
                                {personCards.map((card, idx) => (
                                    <li key={card.key}>
                                        <EventPersonCard
                                            role={card.role}
                                            person={card.person}
                                            className="slide-down py-1 absolute bg-white transition-all duration-250 ease-in sm:w-53 lg:w-40 lg:shrink-0 2xl:w-53"
                                            style={{ '--offset': idx }}
                                        />
                                    </li>
                                ))}
                            </ul>
                        </div>
                    </div>

                    <figure className="max-w-31 shrink-0 md:mr-10 lg:mr-4 lg:max-w-20 xl:max-w-30 2xl:mr-6">
                        <img
                            src={event.organizer?.regalia}
                            alt="Фото регалии"
                        />
                    </figure>

                    <ul className="flex flex-wrap items-baseline gap-2">
                        {event.themes?.map((theme) => (
                            <li
                                key={theme.name}
                                className="rounded-full px-2 py-1 text-xs"
                                style={{ backgroundColor: theme.color_hex }}
                            >
                                {theme.name}
                            </li>
                        ))}
                    </ul>
                </div>
            </CardLayout>
        </li>
    );
};

export default EventCard;
