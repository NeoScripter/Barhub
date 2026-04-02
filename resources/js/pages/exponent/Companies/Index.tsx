import AccentHeading from '@/components/ui/AccentHeading';
import { Button } from '@/components/ui/Button';
import Image from '@/components/ui/Image';
import { edit } from '@/wayfinder/routes/exponent/companies';
import { Inertia } from '@/wayfinder/types';
import { Link } from '@inertiajs/react';
import { Globe, Mail, Pencil, Phone, Send } from 'lucide-react';
import { FC } from 'react';
import InfoItem from './partials/InfoItem';
import Instagram from './partials/Instagram';

const Index: FC<Inertia.Pages.Exponent.Companies.Index> = ({ company }) => {
    return (
        <div className="space-y-8 md:space-y-11 2xl:space-y-13">
            <div className="mb-8 text-center md:mb-12">
                <AccentHeading
                    asChild
                    className="mb-1 text-lg text-secondary"
                >
                    <h2>Информация о компании для сайта и приложения</h2>
                </AccentHeading>
            </div>

            <div className="flex flex-col items-center justify-between gap-4 sm:flex-row">
                <div className="flex flex-col items-center gap-4 sm:flex-row sm:gap-9">
                    {company.logo && (
                        <Image
                            wrapperStyles="max-w-50 xl:max-w-62"
                            imgStyles="object-contain"
                            image={company.logo}
                        />
                    )}

                    <div className="text-center sm:text-left">
                        <AccentHeading
                            asChild
                            className="mb-1 text-xl lg:text-2xl"
                        >
                            <h1>{company.public_name}</h1>
                        </AccentHeading>

                        <p className="lg:text-lg">{company.legal_name}</p>
                    </div>
                </div>
            </div>

            <p className="lg:text-lg xl:text-xl">{company.description}</p>

            <ul className="flex flex-col flex-wrap items-baseline gap-x-10 gap-y-6 md:flex-row">
                {company.phone && (
                    <InfoItem>
                        <Phone />
                        {company.phone}
                    </InfoItem>
                )}
                {company.email && (
                    <InfoItem>
                        <Mail />
                        {company.email}
                    </InfoItem>
                )}
                {company.site_url && (
                    <InfoItem>
                        <Globe />
                        {company.site_url.slice(0, 30)}
                    </InfoItem>
                )}
                {company.telegram && (
                    <InfoItem>
                        <Send />
                        {company.telegram}
                    </InfoItem>
                )}
                {company.instagram && (
                    <InfoItem>
                        <Instagram />
                        {company.instagram}
                    </InfoItem>
                )}
            </ul>

            <AccentHeading
                asChild
                className="text-xl lg:text-2xl"
            >
                <p>
                    Код стенда:{' '}
                    <span className="text-foreground">
                        {company.stand_code}
                    </span>{' '}
                </p>
            </AccentHeading>

            {company.tags && (
                <ul className="flex flex-wrap items-center gap-3">
                    {company.tags.map((tag) => (
                        <li
                            key={tag.id}
                            className="flex items-center rounded-md bg-gray-400 px-4 py-2.5 text-white"
                        >
                            {tag.name}
                        </li>
                    ))}
                </ul>
            )}

            <AccentHeading
                asChild
                className="text-xl lg:text-2xl"
            >
                <h2>Активности:</h2>
            </AccentHeading>

            {company.activities && (
                <p className="lg:text-lg xl:text-xl">{company.activities}</p>
            )}

            <Button
                asChild
                size="lg"
                variant="default"
                className="mx-auto w-fit"
            >
                <Link href={edit({ company: company.id }).url}>
                    <Pencil />
                    Редактировать
                </Link>
            </Button>
        </div>
    );
};

export default Index;
