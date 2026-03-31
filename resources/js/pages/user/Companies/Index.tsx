import CardLayout from '@/components/layout/CardLayout';
import AccentHeading from '@/components/ui/AccentHeading';
import Image from '@/components/ui/Image';
import SearchInput from '@/components/ui/SearchInput';
import { show } from '@/wayfinder/routes/companies';
import { App } from '@/wayfinder/types';
import { Link } from '@inertiajs/react';
import { FC } from 'react';

const Index: FC<{
    exhibition: App.Models.Exhibition;
    companies: App.Models.Company[] | undefined;
}> = ({ exhibition, companies }) => {
    return (
        <div>
            <header className="mb-8 md:mb-12">
                <AccentHeading
                    className="mb-4 text-lg md:mb-6"
                    asChild
                >
                    <h2>Партнеры и экпоненты</h2>
                </AccentHeading>

                <SearchInput placeholder="Поиск по партнерам" />
            </header>

            <ul className="grid grid-cols-[repeat(auto-fill,minmax(14.5rem,1fr))] gap-8">
                {companies?.map((company) => (
                    <li key={company.id}>
                        <CardLayout className="relative w-full p-5 text-foreground ring-primary transition-transform hover:scale-103 hover:ring-2">
                            {company.logo && (
                                <Image
                                    wrapperStyles="w-full mb-4"
                                    image={company.logo}
                                />
                            )}

                            <Link
                                href={show({ exhibition, company }).url}
                                className="absolute inset-0"
                            />

                            <h3 className="mx-auto mb-8 min-h-[3.2em] max-w-4/5 text-center font-bold">
                                {' '}
                                asdsad adsd {company.public_name}
                            </h3>

                            <p className="text-sm">General sponsor</p>
                        </CardLayout>
                    </li>
                ))}
            </ul>
        </div>
    );
};

export default Index;
