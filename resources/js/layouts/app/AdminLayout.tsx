import { AppContent } from '@/components/layout/AppContent';
import AppHeader from '@/components/layout/AppHeader';
import { AppShell } from '@/components/layout/AppShell';
import type { AppLayoutProps } from '@/types';

export default function AdminLayout({ children }: AppLayoutProps) {
    return (
        <AppShell>
            <AppHeader />
            <AppContent>{children}</AppContent>
        </AppShell>
    );
}
