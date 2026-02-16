import CardLayout from '@/components/layout/CardLayout';
import { paddingStyles } from '@/lib/consts/styles';
import { cn, shortenDescription } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import { App } from '@/wayfinder/types';
import { ComponentProps, FC } from 'react';
import EventDateInfo from './EventDateInto';
import PersonCardAccordion from './PersonCardAccordion';
import RegaliaAccordion from './RegaliaAccordion';

const EventCard: FC<
    NodeProps<{ event: App.Models.Event } & ComponentProps<'li'>>
> = ({ className, event, ...props }) => {
    return (
        <li
            {...props}
            className="isolate"
        >
            <CardLayout
                className={cn(
                    className,
                    paddingStyles,
                    'group slide-down-parent w-full items-start gap-6 text-foreground transition-transform duration-150 ease-in-out hover:scale-105 sm:items-center sm:gap-9 lg:flex-row',
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
                    <PersonCardAccordion event={event} />

                    <RegaliaAccordion event={event} />

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
