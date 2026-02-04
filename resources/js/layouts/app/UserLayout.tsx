import { AppContent } from '@/components/layout/AppContent';
import AppHeader from '@/components/layout/AppHeader/AppHeader';
import { AppShell } from '@/components/layout/AppShell';
import { NodeProps } from '@/types/shared';

export default function UserLayout({ children }: NodeProps) {
    return (
        <AppShell>
            <AppHeader variant="user" />
            <AppContent>{children}</AppContent>
        </AppShell>
    );
}
