import CardLayout from '@/components/layout/CardLayout';
import { Button } from '@/components/ui/Button';
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
                    'w-full gap-7 px-7 py-7! text-foreground sm:flex-row sm:justify-between lg:gap-8 lg:px-10 lg:py-11! xl:flex-col xl:items-end',
                    className,
                )}
            >
                <div className="sm:flex sm:gap-5">
                    {person.avatar && (
                        <Image
                            image={person.avatar}
                            wrapperStyles="size-30 shrink-0 mb-7.5 sm:mb-0 mx-auto"
                        />
                    )}

                    <div className="text-center sm:text-left">
                        {person.name && (
                            <h3 className="mb-2 text-xl font-bold">
                                {person.name}
                            </h3>
                        )}
                        {person.regalia && (
                            <h3>{`Регалии: ${person.regalia}`}</h3>
                        )}
                        {person.role_label && (
                            <p className="mt-3 text-lg font-semibold">
                                {person.role_label}
                            </p>
                        )}
                    </div>
                </div>

                <Button
                    asChild
                    variant="accent"
                >
                    <Link href={show({ exhibition, person })}>Подробнее</Link>
                </Button>
            </CardLayout>
        </li>
    );
};

export default PersonCard;
