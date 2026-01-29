import * as React from 'react';

type Props = React.ComponentProps<'main'> & {
    variant?: 'header' | 'sidebar';
};

export function AppContent({  children, ...props }: Props) {
    return (
        <main
            className="flex h-full flex-1 flex-col"
            {...props}
        >
            {children}
        </main>
    );
}
