import GridLayout from '@/components/layout/GridLayout';
import ActionCard from '@/components/ui/ActionCard';
import { Button } from '@/components/ui/Button';
import EventController from '@/wayfinder/App/Http/Controllers/Admin/EventController';
import PersonController from '@/wayfinder/App/Http/Controllers/Admin/PersonController';
import { Inertia } from '@/wayfinder/types';
import { Link } from '@inertiajs/react';
import { BriefcaseBusiness, Calendar, LucideIcon, User } from 'lucide-react';
import { FC } from 'react';

const Show: FC<Inertia.Pages.Admin.Exhibitions.Show> = ({ exhibition }) => {
    return (
        <GridLayout>
            {cards.map((card) => (
                <li key={card.id}>
                    <ActionCard>
                        <ActionCard.Icon icon={card.icon} />
                        <ActionCard.Title>{card.label}</ActionCard.Title>
                        <Button
                            asChild
                            className="w-2/3"
                        >
                            <Link href={card.url(exhibition.id)}>Перейти</Link>
                        </Button>
                    </ActionCard>
                </li>
            ))}
        </GridLayout>
    );
};

export default Show;

type CardLinkType = {
    id: string;
    icon: LucideIcon;
    label: string;
    url: (id: number) => string;
};

const cards: CardLinkType[] = [
    {
        id: crypto.randomUUID(),
        icon: Calendar,
        label: 'События',
        url: (id) => EventController.index({id: id}).url,
    },
    {
        id: crypto.randomUUID(),
        icon: User,
        label: 'Спикеры',
        url: (id) => PersonController.index({id: id}).url,
    },
    {
        id: crypto.randomUUID(),
        icon: BriefcaseBusiness,
        label: 'Компании',
        url: (id) => '/',
    },
];
