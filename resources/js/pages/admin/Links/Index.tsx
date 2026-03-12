import GridLayout from '@/components/layout/GridLayout';
import ActionCard from '@/components/ui/ActionCard';
import EventController from '@/wayfinder/App/Http/Controllers/Admin/EventController';
import PersonController from '@/wayfinder/App/Http/Controllers/Admin/PersonController';
import CompanyController from '@/wayfinder/App/Http/Controllers/Admin/CompanyController';
import { App } from '@/wayfinder/types';
import { FC } from 'react';
import LinkSelector, { LinkOptionType } from './partials/LinkSelector';

type CardLinkType = {
    id: string;
    options: LinkOptionType[];
    label: string;
    name: string;
    url: (id: number) => string;
};

const Index: FC<App.Http.Controllers.Admin.LinkController> = ({
    people,
    companies,
    events,
}) => {
    const cards: CardLinkType[] = [
        {
            id: crypto.randomUUID(),
            options: events,
            label: 'События',
            name: 'событие',
            url: (id) => EventController.index({ id }).url,
        },
        {
            id: crypto.randomUUID(),
            options: people,
            label: 'Спикеры',
            name: 'спикера',
            url: (id) => PersonController.index({ id }).url,
        },
        {
            id: crypto.randomUUID(),
            options: companies,
            label: 'Компании',
            name: 'компанию',
            url: (id) => CompanyController.index({ id }).url,
        },
    ];

    return (
        <GridLayout>
            {cards.map((card) => (
                <li key={card.id}>
                    <ActionCard>
                        <ActionCard.Title>{card.label}</ActionCard.Title>
                        <LinkSelector
                            options={card.options}
                            setter={card.url}
                            label={card.name}
                        />
                    </ActionCard>
                </li>
            ))}
        </GridLayout>
    );
};

export default Index;
