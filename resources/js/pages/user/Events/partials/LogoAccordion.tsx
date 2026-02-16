import Image from '@/components/ui/Image';
import { cn } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import { App } from '@/wayfinder/types';
import { FC } from 'react';
import AccordionLayout from './AccordionLayout';

const LogoAccordion: FC<NodeProps<{ event: App.Models.Event }>> = ({
    event,
}) => {
    const people = event.people;

    return (
        <div className="relative isolate max-w-31 shrink-0 bg-white md:mr-10 lg:mr-4 lg:max-w-20 2xl:mr-6 2xl:max-w-30">
            {people &&
                people.slice(0, 1).map(
                    (person) =>
                        person.logo && (
                            <Image
                                wrapperStyles="w-31 bg-white lg:w-20 2xl:w-30"
                                image={person.logo}
                            />
                        ),
                )}
            <AccordionLayout className="gap-4 lg:gap-6 2xl:gap-4">
                {people &&
                    people.map((person, idx) => (
                        <li key={person.id}>
                            {person.logo && (
                                <Image
                                    wrapperStyles={cn(
                                        'w-31 bg-white lg:w-20 2xl:w-30',
                                        idx === 0 && 'hidden sm:block',
                                    )}
                                    image={person.logo}
                                />
                            )}
                        </li>
                    ))}
            </AccordionLayout>
        </div>
    );
};

export default LogoAccordion;
