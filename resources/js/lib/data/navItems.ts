import DashboardController from '@/wayfinder/App/Http/Controllers/Admin/DashboardController';
import ExhibitionController from '@/wayfinder/App/Http/Controllers/Admin/ExhibitionController';
import {
    BriefcaseBusiness,
    CalendarDays,
    House,
    LucideIcon,
    Martini,
    Star,
    UserCheck,
} from 'lucide-react';

type NavLink = {
    id: string;
    icon: LucideIcon;
    label: string;
    url: string;
    type: 'link';
    isDynamic: boolean;
};

export type NavDrawerType = {
    id: string;
    label: string;
    icon: LucideIcon;
    type: 'drawer';
    links: Omit<NavLink, 'icon'>[];
    isDynamic: boolean;
};

export type NavItemType = NavLink | NavDrawerType;

export const adminNavItems: NavItemType[] = [
    {
        id: 'home',
        type: 'link',
        label: 'Главная',
        url: DashboardController.url(),
        icon: House,
        isDynamic: false,
    },
    {
        id: 'program-events',
        type: 'link',
        label: 'События программы',
        url: '/exhibitions/{exhibition}',
        icon: CalendarDays,
        isDynamic: true,
    },
    {
        id: 'people',
        type: 'link',
        label: 'Люди',
        url: '/exhibitions/{exhibition}/people',
        icon: UserCheck,
        isDynamic: true,
    },
    {
        id: 'companies',
        type: 'link',
        label: 'Компании',
        url: '/exhibitions/{exhibition}/companies',
        icon: BriefcaseBusiness,
        isDynamic: true,
    },
    {
        id: 'partners-work',
        type: 'drawer',
        label: 'Работа с партнерами',
        icon: Star,
        isDynamic: true,
        links: [
            {
                id: 'partner-review-tasks',
                type: 'link',
                label: 'Задачи на проверке',
                url: '/exhibitions/{exhibition}/partner-review-tasks',
                isDynamic: true,
            },
            {
                id: 'partner-all-tasks',
                type: 'link',
                label: 'Общие задачи',
                url: '/exhibitions/{exhibition}/partner-all-tasks',
                isDynamic: true,
            },
            {
                id: 'partner-services',
                type: 'link',
                label: 'Услуги',
                url: '/exhibitions/{exhibition}/partner-services',
                isDynamic: true,
            },
            {
                id: 'partner-company-tags',
                type: 'link',
                label: 'Теги компаний',
                url: '/exhibitions/{exhibition}/partner-company-tags',
                isDynamic: true,
            },
            {
                id: 'partner-materials',
                type: 'link',
                label: 'Информация и материалы',
                url: '/exhibitions/{exhibition}/partner-materials',
                isDynamic: true,
            },
        ],
    },
    {
        id: 'exhibitions',
        type: 'link',
        label: 'Выставки',
        url: ExhibitionController.index.url(),
        icon: Martini,
        isDynamic: false,
    },
];

function extractExhibitionId(url: string): string | null {
    const match = url.match(/exhibitions\/(\d+)/);
    return match ? match[1] : null;
}

function injectExhibitionId(item: NavItemType, exhibitionId: string): NavItemType {
    const newItem = { ...item };

    if (item.type === 'link' && item.isDynamic) {
        newItem.url = item.url.replace('{exhibition}', exhibitionId);
    }

    if (item.type === 'drawer' && item.links) {
        newItem.links = item.links.map(link =>
            injectExhibitionId(link, exhibitionId)
        );
    }

    return newItem;
}

export function renderAdminNavItems(currentUrl: string): NavItemType[] {
    const exhibitionId = extractExhibitionId(currentUrl);

    // Not on an exhibition page - show only non-dynamic items
    if (!exhibitionId) {
        return adminNavItems.filter(item => !item.isDynamic);
    }

    // On an exhibition page - inject the ID into all dynamic URLs
    return adminNavItems.map(item => injectExhibitionId(item, exhibitionId));
}
