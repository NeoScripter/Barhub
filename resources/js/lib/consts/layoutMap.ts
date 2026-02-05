import AdminLayout from '@/layouts/app/AdminLayout';
import UserLayout from '@/layouts/app/UserLayout';
import AuthLayout from '@/layouts/auth/AuthLayout';

const LAYOUT_MAP = {
    'auth/': null,
    'user/': UserLayout,
} as const;

const DEFAULT_LAYOUT = AdminLayout;

export const getLayout = (pageName: string) => {
    const layoutEntry = Object.entries(LAYOUT_MAP).find(([prefix]) =>
        pageName.startsWith(prefix),
    );
    return layoutEntry ? layoutEntry[1] : DEFAULT_LAYOUT;
};
