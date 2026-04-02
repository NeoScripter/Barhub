import AccentHeading from '@/components/ui/AccentHeading';
import SearchInput from '@/components/ui/SearchInput';
import { App } from '@/wayfinder/types';
import { FC } from 'react';
import PersonCard from '../Events/partials/PersonCard';

const Index: FC<{
    exhibition: App.Models.Exhibition;
    people: App.Models.Person[] | undefined;
}> = ({ exhibition, people }) => {
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

            {people && people.length > 0 ? (
                <ul className="mx-auto grid max-w-90 gap-8 sm:max-w-full sm:grid-cols-[repeat(auto-fit,minmax(28rem,1fr))]">
                    {people?.map((person) => (
                        <PersonCard
                            key={person.id}
                            exhibition={exhibition}
                            person={person}
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
