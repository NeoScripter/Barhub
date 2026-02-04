import { AppContent } from '@/components/layout/AppContent';
import AppHeader from '@/components/layout/AppHeader/AppHeader';
import { AppShell } from '@/components/layout/AppShell';
import type { AppLayoutProps } from '@/old-types';

export default function UserLayout({ children }: AppLayoutProps) {
    return (
        <AppShell>
            <AppHeader variant="user" />
            <AppContent>{children}</AppContent>
        </AppShell>
    );
}
