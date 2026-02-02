import { cn } from '@/lib/utils';
import { NodeProps } from '@/types/ui';
import { LucideIcon, Plus } from 'lucide-react';
import { FC } from 'react';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from './Select';

const ActionCard: FC<NodeProps> = ({ className, children }) => {
    return (
        <article
            className={cn(
                'flex w-max flex-col items-center justify-center gap-3 rounded-xl border border-gray-200/40 bg-white px-3 pt-5 pb-6 text-primary shadow-xl sm:gap-3.5 sm:px-3.5 sm:pt-5.5 sm:pb-7 xl:gap-4.5 xl:px-4 xl:pt-12 xl:pb-8.5',
                className,
            )}
        >
            {children}
        </article>
    );
};

export default ActionCard;

type ActionCardIconProps = { icon: LucideIcon; className?: string };

export function ActionCardIcon({ icon, className }: ActionCardIconProps) {
    const Icon = icon;
    return <Icon className={cn('size-9 sm:size-10 xl:size-12', className)} />;
}

type ActionCardBtnProps = NodeProps<{ onClick: () => void }>;

export function ActionCardBtn({
    children,
    className,
    onClick,
}: ActionCardBtnProps) {
    return (
        <button
            onClick={onClick}
            className={cn(
                'inline-flex w-full items-center justify-center gap-1.5 rounded-full bg-primary py-2 pr-6 pl-4 text-xs text-white hover:opacity-75 sm:text-sm xl:py-2.5 [&>svg]:size-3.5 sm:[&>svg]:size-4.5',
                className,
            )}
        >
            <Plus />
            {children}
        </button>
    );
}

export function ActionCardTitle({ children, className }: NodeProps) {
    return (
        <p
            className={cn(
                '-mt-2 text-center font-bold text-foreground uppercase sm:text-xl xl:-mt-3',
                className,
            )}
        >
            {children}
        </p>
    );
}

