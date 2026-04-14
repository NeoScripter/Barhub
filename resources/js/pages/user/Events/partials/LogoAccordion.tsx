import Image from '@/components/ui/Image';
import { cn } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import { App } from '@/wayfinder/types';
import { FC } from 'react';

const LogoAccordion: FC<NodeProps<{ event: App.Models.Event }>> = ({
    event,
}) => {
    const people = event.people;

    return (
        <div className="relative isolate max-w-31 shrink-0 bg-white md:mr-10 lg:mr-4 lg:max-w-20 2xl:mr-6 2xl:max-w-30">
            <ul className="flex flex-col justify-between gap-5">
                {people &&
                    people.map((person, idx) => (
                        <li key={`${person.id} ${idx}`}>
                            {person.logo && (
                                <Image
                                    wrapperStyles={cn(
                                        'size-31 bg-white lg:sizew-20 2xl:size-30',
                                    )}
                                    imgStyles="object-contain"
                                    image={person.logo}
                                />
                            )}
                        </li>
                    ))}
            </ul>
        </div>
    );
};

export default LogoAccordion;
