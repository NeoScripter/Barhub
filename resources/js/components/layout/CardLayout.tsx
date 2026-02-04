import { cn } from '@/lib/utils';
import { NodeProps } from '@/types/shared';

export default function CardLayout({ className, children }: NodeProps) {
    return (
        <article
            className={cn(
                'flex w-max flex-col items-center justify-center rounded-xl border border-gray-200/40 bg-white text-primary shadow-xl',
                className,
            )}
        >
            {children}
        </article>
    );
}
