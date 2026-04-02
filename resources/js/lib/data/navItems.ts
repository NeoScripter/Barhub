import AdminDash from '@/wayfinder/App/Http/Controllers/Admin/DashboardController';
import AdminExpos from '@/wayfinder/App/Http/Controllers/Admin/ExhibitionController';
import LinkController from '@/wayfinder/App/Http/Controllers/Admin/LinkController';
import ExponentDash from '@/wayfinder/App/Http/Controllers/Exponent/TaskController';
import ExponentCompany from '@/wayfinder/App/Http/Controllers/Exponent/CompanyController';
import ExponentFollowups from '@/wayfinder/App/Http/Controllers/Exponent/FollowupController';
import ExponentInfoItems from '@/wayfinder/App/Http/Controllers/Exponent/InfoItemController';
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
import CompanyController from '@/wayfinder/App/Http/Controllers/User/CompanyController';

type NavLink = {
    id: string;
    icon: LucideIcon;
    label: string;
    url: string;
    type: 'link';
};

export type NavDrawerType = {
    id: string;
    label: string;
    icon: LucideIcon;
    type: 'drawer';
    links: Omit<NavLink, 'icon'>[];
};

export type NavItemType = NavLink | NavDrawerType;

export const adminNavItems: NavItemType[] = [
    {
        id: 'home',
        type: 'link',
        label: 'Главная',
        url: AdminDash.index().url,
        icon: House,
    },
    {
        id: 'program-events',
        type: 'link',
        label: 'События программы',
        url: expoIndex.url(),
        icon: CalendarDays,
    },
    {
        id: 'people',
        type: 'link',
        label: 'Люди',
        url: personIndex.url(),
        icon: UserCheck,
    },
    {
        id: 'companies',
        type: 'link',
        label: 'Компании',
        url: '/admin/companies',
        icon: BriefcaseBusiness,
    },
    {
        id: 'partners-work',
        type: 'drawer',
        label: 'Работа с партнерами',
        icon: Star,
        links: [
            {
                id: 'all-tasks',
                type: 'link',
                label: 'Задачи на проверке',
                url: '/admin/all-tasks',
            },
            {
                id: 'task-templates',
                type: 'link',
                label: 'Общие задачи',
                url: '/admin/task-templates',
            },
            {
                id: 'services',
                type: 'link',
                label: 'Услуги',
                url: '/admin/services',
            },
            {
                id: 'followups',
                type: 'link',
                label: 'Заявки на услуги',
                url: '/admin/followups',
            },
            {
                id: 'info-items',
                type: 'link',
                label: 'Информация и материалы',
                url: '/admin/info-items',
            },
        ],
    },
    {
        id: 'exhibitions',
        type: 'link',
        label: 'Выставки',
        url: AdminExpos.index.url(),
        icon: Martini,
    },
    {
        id: 'public links',
        type: 'link',
        label: 'Публичные ссылки',
        url: LinkController.url(),
        icon: CreditCard,
    },
];

export const exponentNavItems: NavItemType[] = [
    {
        id: 'home',
        type: 'link',
        label: 'Главная',
        url: ExponentDash.index.url(),
        icon: House,
    },
    {
        id: 'companies',
        type: 'link',
        label: 'Данные о компании',
        url: ExponentCompany.index().url,
        icon: BriefcaseBusiness,
    },
    {
        id: 'services',
        type: 'link',
        label: 'Услуги',
        url: ExponentFollowups.index().url,
        icon: LayoutDashboard,
    },
    {
        id: 'materials',
        type: 'link',
        label: 'Информация и материалы',
        url: ExponentInfoItems.index().url,
        icon: ListChecks,
    },
];

export const userNavItems: NavItemType[] = [
    {
        id: 'expos',
        type: 'link',
        label: 'Выставки',
        url: UserExpos.url(),
        icon: Martini,
    },
    {
        id: 'admin-panel',
        type: 'link',
        label: 'Админка руководителя',
        url: AdminDash.index().url,
        icon: UserPen,
    },
    {
        id: 'admin-panel',
        type: 'link',
        label: 'Личный кабинет экспонента',
        url: ExponentDash.index.url(),
        icon: UserSearch,
    },
    {
        id: 'people',
        type: 'link',
        label: 'Спикеры и организаторы',
        url: '/',
        icon: Users,
    },
    {
        id: 'services',
        type: 'link',
        label: 'Партнеры и экспоненты',
        url: CompanyController.index.url({exhibition: 1}),
        icon: Handshake,
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
