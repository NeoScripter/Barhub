import AccentHeading from '@/components/ui/AccentHeading';
import { Inertia } from '@/wayfinder/types';
import { FC } from 'react';
import ServiceCard from './partials/ServiceCard';
import FollowupCard from './partials/FollowupCard';

const Index: FC<Inertia.Pages.Exponent.Followups.Index> = ({
    followups,
    services,
    company,
}) => {
    return (
        <div className="space-y-30!">
            <div className="space-y-8">
                <AccentHeading className="text-center text-xl lg:text-2xl">
                    Услуги
                </AccentHeading>
                <AccentHeading className="text-center text-xl text-secondary lg:text-2xl">
                    Согласованние параметры размещения
                </AccentHeading>

                <ul className="flex flex-col flex-wrap items-baseline justify-between gap-x-10 gap-y-6 pt-4 md:flex-row">
                    <AccentHeading
                        asChild
                        className="text-xl lg:text-2xl"
                    >
                        <li>
                            Код стенда:{' '}
                            <span className="text-foreground">
                                {company.stand_code}
                            </span>{' '}
                        </li>
                    </AccentHeading>
                    <AccentHeading
                        asChild
                        className="text-xl lg:text-2xl"
                    >
                        <li>
                            Площадь стенда:{' '}
                            <span className="text-foreground">
                                {`${company.stand_area} кв. м.`}
                            </span>{' '}
                        </li>
                    </AccentHeading>
                    <AccentHeading
                        asChild
                        className="text-xl lg:text-2xl"
                    >
                        <li>
                            Электричество:{' '}
                            <span className="text-foreground">
                                {`${company.power_kw} кВт`}
                            </span>{' '}
                        </li>
                    </AccentHeading>
                    <AccentHeading
                        asChild
                        className="text-xl lg:text-2xl"
                    >
                        <li>
                            Склад:{' '}
                            <span className="text-foreground">
                                {company.storage_enabled ? 'Да' : 'Нет'}
                            </span>{' '}
                        </li>
                    </AccentHeading>
                </ul>
            </div>
            <div>
                <AccentHeading className="mb-10 text-center text-xl text-secondary lg:text-2xl">
                    Подать заявку на услугу
                </AccentHeading>

                <ul className="grid gap-6 sm:grid-cols-[repeat(auto-fit,minmax(20rem,1fr))] lg:gap-7 xl:grid-cols-3 xl:gap-8">
                    {services.map((service) => (
                        <ServiceCard
                            key={service.id}
                            service={service}
                            className="transition:all duration-150 hover:border-primary"
                        />
                    ))}
                </ul>
            </div>
            <div>
                <AccentHeading className="mb-10 text-center text-xl text-secondary lg:text-2xl">
                    Заказанные услуги
                </AccentHeading>

                <ul className="grid gap-6 sm:grid-cols-[repeat(auto-fit,minmax(20rem,1fr))] lg:gap-7 xl:grid-cols-3 xl:gap-8">
                    {followups.map((followup) => (
                        <FollowupCard
                            key={followup.id}
                            followup={followup}
                        />
                    ))}
                </ul>
            </div>
        </div>
    );
};

export default Index;
