import { cn } from '@/lib/utils';
import { NodeProps } from '@/types/ui';
import { FC, ReactNode } from 'react';
import CardLayout from '../layout/CardLayout';

interface TaskCardComposition {
    Badge: typeof TaskCardBadge;
    Digit: typeof TaskCardDigit;
    Label: typeof TaskCardLabel;
}

interface BadgeProps {
    children: ReactNode;
    className?: string;
    variant?: 'default' | 'success' | 'warning' | 'danger';
}

interface DigitProps {
    value: number;
    className?: string;
}

interface LabelProps {
    children: ReactNode;
    className?: string;
}

const TaskCard: FC<NodeProps> & TaskCardComposition = ({
    className,
    children,
}) => {
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
};

TaskCard.displayName = 'TaskCard';

const BADGE_VARIANTS = {
    default: 'bg-gray-500',
    success: 'bg-green-600',
    warning: 'bg-amber-600',
    danger: 'bg-red-800',
} as const;

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
            aria-label="Task badge"
        >
            {children}
        </div>
    );
};

TaskCardBadge.displayName = 'TaskCard.Badge';

const TaskCardDigit: FC<DigitProps> = ({ value, className }) => {
    return (
        <div
            className={cn(
                'mx-auto w-fit text-5xl font-bold text-primary',
                'sm:text-6xl xl:text-8xl',
                className,
            )}
            role="status"
            aria-label={`Count: ${value}`}
        >
            {value.toLocaleString()}
        </div>
    );
};

TaskCardDigit.displayName = 'TaskCard.Digit';

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
