import { cn } from '@/lib/utils';

export default function NavDrawer({ show }: { show: boolean }) {
    return (
        <div
            id="nav-drawer"
            role="dialog"
            aria-modal="true"
            aria-label="Навигационное меню"
            className={cn(
                'bg-background ease-nav-drawer fixed top-0 right-0 z-0 w-80 max-w-full overflow-y-auto rounded-bl-[2rem] bg-cover bg-top-left bg-no-repeat px-8 py-7 transition-transform duration-1000',
                !show && 'translate-x-full',
            )}
        >
            Hello world
        </div>
    );
}
