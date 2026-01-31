import {
    BriefcaseBusiness,
    CalendarDays,
    House,
    LucideIcon,
    Martini,
    UserCheck,
} from 'lucide-react';

type NavLink = {
    id: string;
    icon: LucideIcon;
    label: string;
    url: string;
    type: 'link';
};
type NavDrawer = {
    id: string;
    label: string;
    type: 'drawer';
    links: NavLink[];
};
export type NavItemType = NavLink | NavDrawer;

export const navItems: NavItemType[] = [
    {
        id: crypto.randomUUID(),
        type: 'link',
        label: 'Главная',
        url: '/',
        icon: House,
    },
    {
        id: crypto.randomUUID(),
        type: 'link',
        label: 'События программы',
        url: '/',
        icon: CalendarDays,
    },
    {
        id: crypto.randomUUID(),
        type: 'link',
        label: 'Люди',
        url: '/',
        icon: UserCheck,
    },
    {
        id: crypto.randomUUID(),
        type: 'link',
        label: 'Компании',
        url: '/',
        icon: BriefcaseBusiness,
    },
    {
        id: crypto.randomUUID(),
        type: 'drawer',
        label: 'Работа с партнерами',
        url: '/',
        links: [
            {
                id: crypto.randomUUID(),
                type: 'link',
                label: 'Задачи на проверке',
                url: '/',
            },
            {
                id: crypto.randomUUID(),
                type: 'link',
                label: 'Общие задачи',
                url: '/',
            },
            {
                id: crypto.randomUUID(),
                type: 'link',
                label: 'Услуги',
                url: '/',
            },
            {
                id: crypto.randomUUID(),
                type: 'link',
                label: 'Теги компаний',
                url: '/',
            },
            {
                id: crypto.randomUUID(),
                type: 'link',
                label: 'Информация и материалы',
                url: '/',
            },
        ],
    },
    {
        id: crypto.randomUUID(),
        type: 'link',
        label: 'Выставки',
        url: '/',
        icon: Martini,
    },
];
