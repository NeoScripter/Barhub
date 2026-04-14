import AccentHeading from '@/components/ui/AccentHeading';
import { cn, getFilterUrl, isActiveFilter } from '@/lib/utils';
import { App } from '@/wayfinder/types';
import { Link } from '@inertiajs/react';
import { FC } from 'react';
import PersonCard from '../Events/partials/PersonCard';

const Index: FC<{
    exhibition: App.Models.Exhibition;
    people: App.Models.Person[] | undefined;
    roles: { label: string; key: string }[];
}> = ({ exhibition, people, roles }) => {
    return (
        <div>
            <header className="mb-8 md:mb-12">
                <AccentHeading
                    className="mb-6 text-lg md:mb-8"
                    asChild
                >
                    <h2>Спикеры и организаторы</h2>
                </AccentHeading>

                <ul className="flex flex-wrap gap-x-4 gap-y-3">
                    {roles.map((role, idx) => (
                        <FilterBtn
                            key={idx}
                            label={role.label}
                            url={getFilterUrl('roles', role.key)}
                            isActive={isActiveFilter('roles', role.key)}
                        />
                    ))}
                </ul>
            </header>

            {people && people.length > 0 ? (
                <ul className="mx-auto grid max-w-90 gap-8 sm:max-w-full sm:grid-cols-[repeat(auto-fill,minmax(21rem,1fr))]">
                    {people?.map((person) => (
                        <PersonCard
                            key={person.id}
                            exhibition={exhibition}
                            person={person}
                        />
                    ))}
                </ul>
            ) : (
                <p>Не найдено ни одного спикера</p>
            )}
        </div>
    );
};

export default Index;

const FilterBtn: FC<{ label: string; isActive?: boolean; url: string }> = ({
    label,
    isActive = false,
    url,
}) => {
    return (
        <li
            className={cn(
                'link-hover rounded-full border border-primary px-5 py-2 text-primary transition-[colors,opacity] select-none 2xl:text-lg',
                isActive && 'bg-primary text-white',
            )}
        >
            <Link
                href={url}
                preserveScroll
                preserveState
            >
                {label}
            </Link>
        </li>
    );
};
