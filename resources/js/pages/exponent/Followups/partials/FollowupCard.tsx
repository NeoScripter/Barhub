import CardLayout from '@/components/layout/CardLayout';
import Badge from '@/components/ui/Badge';
import { cn } from '@/lib/utils';
import { App } from '@/wayfinder/types';
import { FC } from 'react';

const FollowupCard: FC<{
    followup: App.Models.Followup;
    className?: string;
}> = ({ followup, className }) => {
    const [statusLabel, variant] = (() => {
        switch (followup.status) {
            case 'Закрыт':
                return ['Подтверждена', 'success'];
            case 'Открыт':
                return ['На согласовании', 'warning'];
            default:
                return ['Отклонена', 'danger'];
        }
    })();

    return (
        <li>
            <CardLayout
                className={cn(
                    'relative w-full p-7.5 text-foreground sm:py-9 md:px-8 lg:px-10 lg:py-11',
                    className,
                )}
            >
                <Badge
                    className="relative bottom-2 lg:bottom-4"
                    variant={variant}
                >
                    {statusLabel}
                </Badge>
                <h3 className="mt-2 mb-3 text-center text-lg font-bold text-balance sm:mb-3 sm:text-xl md:mb-4 lg:mb-5">
                    {followup.name}
                </h3>
                <p className="text-sm whitespace-pre-line sm:text-base">
                    {followup.description}
                </p>
            </CardLayout>
        </li>
    );
};

export default FollowupCard;
