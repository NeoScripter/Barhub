import { cn } from '@/lib/utils';
import { NodeProps } from '@/old-types/ui';
import { FC, ReactNode } from 'react';
import CardLayout from '../layout/CardLayout';

const BADGE_VARIANTS = {
    default: 'bg-gray-500',
    success: 'bg-green-600',
    warning: 'bg-amber-600',
    danger: 'bg-red-800',
} as const;

type BadgeProps = {
    children: ReactNode;
    className?: string;
    variant?: 'default' | 'success' | 'warning' | 'danger';
};

type DigitProps = {
    value: number;
    className?: string;
};

type LabelProps = {
    children: ReactNode;
    className?: string;
};

function TaskCard({ className, children }: NodeProps) {
    return (
        <CardLayout
            className={cn(
                'aspect-square gap-2 px-2 py-4 sm:gap-3 xl:gap-4 xl:py-6',
                className,
            )}
        >
            {children}
        </CardLayout>
    );
}

const TaskCardBadge: FC<BadgeProps> = ({
    children,
    className,
    variant = 'default',
}) => {
    return (
        <div
            className={cn(
                'mx-auto w-fit rounded-full px-3 py-1 text-center text-xs font-medium text-white',
                'xl:px-6 xl:py-2 xl:text-sm',
                BADGE_VARIANTS[variant],
                className,
            )}
            role="status"
        >
            {children}
        </div>
    );
};

const TaskCardDigit: FC<DigitProps> = ({ value, className }) => {
    return (
        <div
            className={cn(
                'mx-auto w-fit text-5xl font-bold text-primary',
                'sm:text-6xl xl:text-8xl',
                className,
            )}
            role="status"
        >
            {value.toLocaleString()}
        </div>
    );
};

const TaskCardLabel: FC<LabelProps> = ({ children, className }) => {
    return (
        <p
            className={cn(
                'mx-auto -mt-2 w-fit text-sm font-semibold text-foreground',
                'sm:-mt-3 sm:text-base xl:-mt-4 xl:text-2xl',
                className,
            )}
        >
            {children}
        </p>
    );
};

TaskCard.Badge = TaskCardBadge;
TaskCard.Digit = TaskCardDigit;
TaskCard.Label = TaskCardLabel;

export default TaskCard;
