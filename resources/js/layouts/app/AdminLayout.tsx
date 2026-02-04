import { AppContent } from '@/components/layout/AppContent';
import AppHeader from '@/components/layout/AppHeader/AppHeader';
import { AppShell } from '@/components/layout/AppShell';
import NavMenu from '@/components/layout/AppHeader/partials/NavMenu';
import type { AppLayoutProps } from '@/old-types';

export default function AdminLayout({ children }: AppLayoutProps) {
    return (
        <AppShell>
            <AppHeader />
            <NavMenu className='hidden lg:block' />
            <AppContent>{children}</AppContent>
        </AppShell>
    );
}
