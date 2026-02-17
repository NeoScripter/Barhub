import ActionCard from '@/components/ui/ActionCard';
import { Button } from '@/components/ui/Button';
import { Inertia } from '@/wayfinder/types';
import { Link } from '@inertiajs/react';
import { BriefcaseBusiness, Calendar, LucideIcon, User } from 'lucide-react';
import { FC } from 'react';

const Show: FC<Inertia.Pages.Admin.Exhibitions.Show> = ({ exhibition }) => {
    return (
        <ul className="grid grid-cols-[repeat(auto-fit,minmax(17rem,1fr))] justify-between gap-6 sm:gap-8">
            {cards.map((card) => (
                <li key={card.id}>
                    <ActionCard className="mx-auto w-full sm:mx-0 max-w-80 sm:max-w-full">
                        <ActionCard.Icon icon={card.icon} />
                        <ActionCard.Title>{card.label}</ActionCard.Title>
                        <Button
                            asChild
                            className="w-2/3"
                        >
                            <Link href={card.url}>Перейти</Link>
                        </Button>
                    </ActionCard>
                </li>
            ))}
        </ul>
    );
};

export default Show;

type CardLinkType = {
    id: string;
    icon: LucideIcon;
    label: string;
    url: string;
};

const cards: CardLinkType[] = [
    {
        id: crypto.randomUUID(),
        icon: Calendar,
        label: 'События',
        url: '/',
    },
    {
        id: crypto.randomUUID(),
        icon: User,
        label: 'Спикеры',
        url: '/',
    },
    {
        id: crypto.randomUUID(),
        icon: BriefcaseBusiness,
        label: 'Компании',
        url: '/',
    },
];
