import CardLayout from '@/components/layout/CardLayout';
import Image from '@/components/ui/Image';
import { cn } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import { show } from '@/wayfinder/routes/people';
import { App } from '@/wayfinder/types';
import { Link } from '@inertiajs/react';
import { FC } from 'react';

const PersonCard: FC<
    NodeProps<{ person: App.Models.Person; exhibition: App.Models.Exhibition }>
> = ({ className, exhibition, person }) => {
    return (
        <li>
            <CardLayout
                className={cn(
                    'relative h-full w-full gap-7 px-7 py-7! text-foreground transition-transform duration-150 ease-in-out hover:scale-103 hover:ring-2 hover:ring-primary sm:flex-row sm:justify-between lg:gap-8 xl:flex-col xl:items-end',
                    className,
                )}
            >
                <Link
                    className="absolute inset-0"
                    href={show({ exhibition, person })}
                />
                <div className="sm:flex sm:gap-5 h-full">
                    {person.avatar && (
                        <Image
                            image={person.avatar}
                            wrapperStyles="size-25 shrink-0 mb-7.5 sm:mb-0 mx-auto"
                        />
                    )}

                    <div className="flex h-full flex-col justify-between text-center sm:text-left">
                        {person.name && (
                            <h3 className="mb-2 text-lg font-bold">
                                {person.name}
                            </h3>
                        )}
                        {person.regalia && (
                            <h3 className="text-sm h-full">{`Регалии: ${person.regalia}`}</h3>
                        )}
                        {person.role_label && (
                            <p className="mt-3 text-base font-semibold">
                                {person.role_label}
                            </p>
                        )}
                    </div>
                </div>
            </CardLayout>
        </li>
    );
};

export default PersonCard;
