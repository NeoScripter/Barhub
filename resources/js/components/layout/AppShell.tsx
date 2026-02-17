import type { ReactNode } from 'react';
import { Toaster } from 'sonner';

type Props = {
    children: ReactNode;
};

export function AppShell({ children }: Props) {
    return (
        <div className="mx-auto flex min-h-screen max-w-480 flex-col px-3.5 pb-22 sm:px-10 sm:pb-20.5 lg:px-30 lg:pb-25 2xl:px-55 2xl:pb-28">
            {children}
            <Toaster
                toastOptions={{
                    style: {
                        color: '#ED4B97',
                        borderColor: '#ED4B97',
                    },
                }}
                position="top-center"
            />
        </div>
    );
}
