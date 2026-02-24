import AdminDash from '@/wayfinder/App/Http/Controllers/Admin/DashboardController';
import AdminExpos from '@/wayfinder/App/Http/Controllers/Admin/ExhibitionController';
import ExponentDash from '@/wayfinder/App/Http/Controllers/Exponent/DashboardController';
import UserExpos from '@/wayfinder/App/Http/Controllers/User/ExhibitionController';
import { index as expoIndex } from '@/wayfinder/routes/admin/exhibitions/events';
import { index as personIndex } from '@/wayfinder/routes/admin/exhibitions/people';
import {
    BriefcaseBusiness,
    CalendarDays,
    Handshake,
    House,
    LayoutDashboard,
    ListChecks,
    LucideIcon,
    Martini,
    Star,
    UserCheck,
    Users,
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
        url: AdminDash.url(),
        icon: House,
        isDynamic: false,
    },
    {
        id: 'program-events',
        type: 'link',
        label: 'События программы',
        url: expoIndex.url('{exhibition}'),
        icon: CalendarDays,
        isDynamic: true,
    },
    {
        id: 'people',
        type: 'link',
        label: 'Люди',
        url: personIndex.url('{exhibition}'),
        icon: UserCheck,
        isDynamic: true,
    },
    {
        id: 'companies',
        type: 'link',
        label: 'Компании',
        url: '/admin/exhibitions/{exhibition}/companies',
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
        url: AdminExpos.index.url(),
        icon: Martini,
        isDynamic: false,
    },
];

export const exponentNavItems: NavItemType[] = [
    {
        id: 'home',
        type: 'link',
        label: 'Главная',
        url: ExponentDash.url(),
        icon: House,
        isDynamic: false,
    },
    {
        id: 'companies',
        type: 'link',
        label: 'Задачи',
        url: '/',
        icon: BriefcaseBusiness,
        isDynamic: false,
    },
    {
        id: 'services',
        type: 'link',
        label: 'Услуги',
        url: '/',
        icon: LayoutDashboard,
        isDynamic: false,
    },
    {
        id: 'materials',
        type: 'link',
        label: 'Информация и материалы',
        url: '/',
        icon: ListChecks,
        isDynamic: false,
    },
];

export const userNavItems: NavItemType[] = [
    {
        id: 'expos',
        type: 'link',
        label: 'Выставки',
        url: UserExpos.url(),
        icon: Martini,
        isDynamic: false,
    },
    {
        id: 'people',
        type: 'link',
        label: 'Спикеры и организаторы',
        url: '/',
        icon: Users,
        isDynamic: false,
    },
    {
        id: 'services',
        type: 'link',
        label: 'Партнеры и экспоненты',
        url: '/',
        icon: Handshake,
        isDynamic: false,
    },
];

function extractExhibitionId(url: string): string | null {
    const match = url.match(/admin\/exhibitions\/(\d+)/);
    return match ? match[1] : null;
}

function injectExhibitionId(
    item: NavItemType,
    exhibitionId: string,
): NavItemType {
    const newItem = { ...item };

    if (item.type === 'link' && item.isDynamic) {
        newItem.url = item.url.replace('{exhibition}', exhibitionId);
    }

    if (item.type === 'drawer' && item.links) {
        newItem.links = item.links.map((link) =>
            injectExhibitionId(link, exhibitionId),
        );
    }

    return newItem;
}

export function renderAdminNavItems(
    currentUrl: string,
    canViewExpos: boolean,
): NavItemType[] {
    const exhibitionId = extractExhibitionId(currentUrl);

    if (!canViewExpos) {
        return adminNavItems.filter(
            (item) =>
                item.type === 'link' &&
                !item.url.includes('admin/exhibitions') &&
                !item.isDynamic,
        );
    }

    // Not on an exhibition page - show only non-dynamic items
    if (!exhibitionId) {
        return adminNavItems.filter((item) => !item.isDynamic);
    }

    // On an exhibition page - inject the ID into all dynamic URLs
    return adminNavItems.map((item) =>
        injectExhibitionId(item, exhibitionId),
    );
}

export function renderExponentNavItems(): NavItemType[] {
    return exponentNavItems;
}

export function renderUserNavItems(): NavItemType[] {
    return userNavItems;
}

export function renderNavItems(
    currentUrl: string,
    canViewExpos: boolean,
): NavItemType[] {
    if (currentUrl.includes('/admin/')) {
        return renderAdminNavItems(currentUrl, canViewExpos);
    } else if (currentUrl.includes('/exponent/')) {
        return renderExponentNavItems();
    } else {
        return renderUserNavItems();
    }
}
