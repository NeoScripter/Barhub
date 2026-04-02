import CardLayout from '@/components/layout/CardLayout';
import AccentHeading from '@/components/ui/AccentHeading';
import Image from '@/components/ui/Image';
import SearchInput from '@/components/ui/SearchInput';
import { cn } from '@/lib/utils';
import { show } from '@/wayfinder/routes/companies';
import { App } from '@/wayfinder/types';
import { Link } from '@inertiajs/react';
import { FC } from 'react';

const Index: FC<{
    exhibition: App.Models.Exhibition;
    companies: App.Models.Company[] | undefined;
}> = ({ exhibition, companies }) => {
    const highlight = companies?.findIndex((company) =>
        company?.tags?.some(
            (tag) => tag.name.toLowerCase() === 'генеральный партнер',
        ),
    );

    if (highlight && companies) {
        companies = [
            companies[highlight],
            ...companies.slice(0, highlight),
            ...companies.slice(highlight + 1),
        ];
    }

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

            <ul className="grid grid-cols-[repeat(auto-fill,minmax(15rem,1fr))] gap-8">
                {companies?.map((company, idx) => (
                    <li
                        key={company.id}
                        className={cn(
                            idx === 0 && 'sm:col-span-2 sm:row-span-2',
                        )}
                    >
                        <CardLayout className="relative w-full p-5 text-foreground ring-primary transition-transform hover:scale-103 hover:ring-2">
                            {company.logo && (
                                <Image
                                    wrapperStyles={cn(
                                        'mb-4 w-full',
                                        idx === 0 && 'mb-8',
                                    )}
                                    imgStyles="object-contain"
                                    image={company.logo}
                                />
                            )}

                            <Link
                                href={show({ exhibition, company }).url}
                                className="absolute inset-0"
                            />

                            <h3
                                className={cn(
                                    'mx-auto mb-8 min-h-[3.2em] max-w-4/5 text-center font-bold',
                                    idx === 0 && 'text-2xl',
                                )}
                            >
                                {' '}
                                {company.public_name}
                            </h3>

                            <p
                                className={cn(
                                    'min-h-[3.2em] text-center text-sm',
                                    idx === 0 && 'text-lg',
                                )}
                            >
                                {company.tags
                                    ?.map((tag) => tag.name)
                                    .join(', ')}
                            </p>
                        </CardLayout>
                    </li>
                ))}
            </ul>
        </div>
    );
};

export default Index;
