import AccentHeading from '@/components/ui/AccentHeading';
import SearchInput from '@/components/ui/SearchInput';
import { App } from '@/wayfinder/types';
import { FC } from 'react';
import PartnerCard from './partials/PartnerCard';

const Index: FC<{
    exhibition: App.Models.Exhibition;
    companies: App.Models.Company[] | undefined;
}> = ({ exhibition, companies }) => {

    const highlight = companies?.findIndex((company) =>
        company?.tags?.some(
            (tag) => tag.name.toLowerCase() === 'генеральный партнер',
        ),
    );

    if (highlight && highlight !== -1 && companies) {
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

            {companies && companies.length > 0 ? (
                <ul className="grid gap-8 sm:grid-cols-[repeat(auto-fit,minmax(12rem,1fr))]">
                    {companies?.map((company, idx) => (
                        <PartnerCard
                            key={company.id}
                            exhibition={exhibition}
                            company={company}
                            highlighted={idx === 0}
                        />
                    ))}
                </ul>
            ) : (
                <p>Не найдено ни одной компании</p>
            )}
        </div>
    );
};

export default Index;
