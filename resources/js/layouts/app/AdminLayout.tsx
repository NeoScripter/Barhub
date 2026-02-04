import { AppContent } from '@/components/layout/AppContent';
import AppHeader from '@/components/layout/AppHeader/AppHeader';
import { AppShell } from '@/components/layout/AppShell';
import NavMenu from '@/components/layout/AppHeader/partials/NavMenu';
import { NodeProps } from '@/types/shared';

export default function AdminLayout({ children }: NodeProps) {
    return (
        <AppShell>
            <AppHeader />
            <NavMenu className='hidden lg:block' />
            <AppContent>{children}</AppContent>
        </AppShell>
    );
}
