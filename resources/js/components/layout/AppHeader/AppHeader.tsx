import { cn } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import { App } from '@/wayfinder/types';
import { usePage } from '@inertiajs/react';
import { FC } from 'react';
import AccentHeading from '../../ui/AccentHeading';
import AppLogo from '../AppLogo';
import AccountMenuControls from './partials/AccountMenuControls';

type Props = NodeProps & {
    variant?: 'user' | 'admin';
};

const AppHeader: FC<Props> = ({ variant = 'admin' }) => {
    const isAdmin = variant === 'admin';
    const { exhibition } = usePage<{
        exhibition?: App.Models.Exhibition;
    }>().props;

    return (
        <header
            className={cn(
                'flex items-center justify-between gap-2 py-6.5 sm:py-9.5 xl:py-11',
                { 'sm:items-baseline': !isAdmin },
            )}
        >
            <AppLogo />
            {exhibition && (
                <AccentHeading
                    asChild
                    className={cn('sm:align-baseline', {
                        'hidden lg:block': isAdmin,
                        'text-right': !isAdmin,
                    })}
                >
                    <h1>{exhibition.name}</h1>
                </AccentHeading>
            )}

            {isAdmin && <AccountMenuControls />}
        </header>
    );
};

export default AppHeader;
