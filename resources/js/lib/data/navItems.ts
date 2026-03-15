import AdminDash from '@/wayfinder/App/Http/Controllers/Admin/DashboardController';
import AdminExpos from '@/wayfinder/App/Http/Controllers/Admin/ExhibitionController';
import LinkController from '@/wayfinder/App/Http/Controllers/Admin/LinkController';
import ExponentDash from '@/wayfinder/App/Http/Controllers/Exponent/DashboardController';
import UserExpos from '@/wayfinder/App/Http/Controllers/User/ExhibitionController';
import { index as expoIndex } from '@/wayfinder/routes/admin/events';
import { index as personIndex } from '@/wayfinder/routes/admin/people';
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
    UserPen,
    UserSearch,
    CreditCard
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
        url: AdminDash.index().url,
        icon: House,
        isDynamic: false,
    },
    {
        id: 'program-events',
        type: 'link',
        label: 'События программы',
        url: expoIndex.url(),
        icon: CalendarDays,
        isDynamic: true,
    },
    {
        id: 'people',
        type: 'link',
        label: 'Люди',
        url: personIndex.url(),
        icon: UserCheck,
        isDynamic: true,
    },
    {
        id: 'companies',
        type: 'link',
        label: 'Компании',
        url: '/admin/companies',
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
                id: 'all-tasks',
                type: 'link',
                label: 'Задачи на проверке',
                url: '/admin/all-tasks',
                isDynamic: true,
            },
            {
                id: 'task-templates',
                type: 'link',
                label: 'Общие задачи',
                url: '/admin/task-templates',
                isDynamic: true,
            },
            {
                id: 'followups',
                type: 'link',
                label: 'Услуги',
                url: '/admin/followups',
                isDynamic: true,
            },
            {
                id: 'info-items',
                type: 'link',
                label: 'Информация и материалы',
                url: '/admin/info-items',
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
    {
        id: 'public links',
        type: 'link',
        label: 'Публичные ссылки',
        url: LinkController.url(),
        icon: CreditCard,
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
        id: 'admin-panel',
        type: 'link',
        label: 'Админка руководителя',
        url: AdminDash.index().url,
        icon: UserPen,
        isDynamic: false,
    },
    {
        id: 'admin-panel',
        type: 'link',
        label: 'Личный кабинет экспонента',
        url: ExponentDash.url(),
        icon: UserSearch,
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

export function renderAdminNavItems(): NavItemType[] {
    return adminNavItems;
}

export function renderExponentNavItems(): NavItemType[] {
    return exponentNavItems;
}

export function renderUserNavItems(): NavItemType[] {
    return userNavItems;
}

export function renderNavItems(
    currentUrl: string,
): NavItemType[] {
    if (currentUrl.includes('/admin/')) {
        return renderAdminNavItems();
    } else if (currentUrl.includes('/exponent/')) {
        return renderExponentNavItems();
    } else {
        return renderUserNavItems();
    }
}
