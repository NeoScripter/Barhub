import { AppContent } from '@/components/layout/AppContent';
import AppHeader from '@/components/layout/AppHeader';
import { AppShell } from '@/components/layout/AppShell';
import type { AppLayoutProps } from '@/types';

export default function AppLayout({ children }: AppLayoutProps) {
    return (
        <AppShell>
            <AppHeader />
            <AppContent className="overflow-x-hidden">{children}</AppContent>
        </AppShell>
    );
}
