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
};

export type NavDrawerType = {
    id: string;
    label: string;
    icon: LucideIcon;
    type: 'drawer';
    links: Omit<NavLink, 'icon'>[];
};

export type NavItemType = NavLink | NavDrawerType;

export const navItems: NavItemType[] = [
    {
        id: 'home',
        type: 'link',
        label: 'Главная',
        url: DashboardController.url(),
        icon: House,
    },
    {
        id: 'program-events',
        type: 'link',
        label: 'События программы',
        url: '/',
        icon: CalendarDays,
    },
    {
        id: 'people',
        type: 'link',
        label: 'Люди',
        url: '/',
        icon: UserCheck,
    },
    {
        id: 'companies',
        type: 'link',
        label: 'Компании',
        url: '/',
        icon: BriefcaseBusiness,
    },
    {
        id: 'partners-work',
        type: 'drawer',
        label: 'Работа с партнерами',
        icon: Star,
        links: [
            {
                id: 'partner-review-tasks',
                type: 'link',
                label: 'Задачи на проверке',
                url: '/',
            },
            {
                id: 'partner-all-tasks',
                type: 'link',
                label: 'Общие задачи',
                url: '/',
            },
            {
                id: 'partner-services',
                type: 'link',
                label: 'Услуги',
                url: '/',
            },
            {
                id: 'partner-company-tags',
                type: 'link',
                label: 'Теги компаний',
                url: '/',
            },
            {
                id: 'partner-materials',
                type: 'link',
                label: 'Информация и материалы',
                url: '/',
            },
        ],
    },
    {
        id: 'exhibitions',
        type: 'link',
        label: 'Выставки',
        url: ExhibitionController.index.url(),
        icon: Martini,
    },
];
