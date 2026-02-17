import GridLayout from '@/components/layout/GridLayout';
import ActionCard from '@/components/ui/ActionCard';
import { Button } from '@/components/ui/Button';
import { index } from '@/wayfinder/routes/admin/exhibitions/events';
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
                            <Link href={card.url(exhibition.slug)}>Перейти</Link>
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
    url: (slug: string) => string;
};

const cards: CardLinkType[] = [
    {
        id: crypto.randomUUID(),
        icon: Calendar,
        label: 'События',
        url: (slug) => index({slug: slug}).url,
    },
    {
        id: crypto.randomUUID(),
        icon: User,
        label: 'Спикеры',
        url: (slug) => '/',
    },
    {
        id: crypto.randomUUID(),
        icon: BriefcaseBusiness,
        label: 'Компании',
        url: (slug) => '/',
    },
];
