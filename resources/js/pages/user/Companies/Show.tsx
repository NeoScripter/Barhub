import AccentHeading from '@/components/ui/AccentHeading';
import Image from '@/components/ui/Image';
import { Globe, Mail, Phone, Send } from 'lucide-react';
import { FC } from 'react';

import InfoItem from '@/pages/exponent/Companies/partials/InfoItem';
import Instagram from '@/pages/exponent/Companies/partials/Instagram';
import { App } from '@/wayfinder/types';

const Show: FC<{ company: App.Models.Company }> = ({ company }) => {
    return (
        <div className="space-y-12 md:space-y-14 2xl:space-y-16">
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
                        {company.site_url.slice(0, 20)}
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

            <div>
                <AccentHeading
                    asChild
                    className="mb-4 text-xl lg:mb-6 lg:text-2xl"
                >
                    <h2>Активности:</h2>
                </AccentHeading>

                {company.activities && (
                    <p className="lg:text-lg xl:text-xl">
                        {company.activities}
                    </p>
                )}
            </div>
        </div>
    );
};

export default Show;
