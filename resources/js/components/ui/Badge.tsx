import { cn } from '@/lib/utils';
import { NodeProps } from '@/types/ui';
import { cva, VariantProps } from 'class-variance-authority';
import { FC } from 'react';

const badgeVariants = cva('inline-flex w-fit rounded-[0.65rem] justify-center py-1 font-semibold px-2.5 border uppercase', {
    variants: {
        variant: {
            success: 'border-badge-success/50 bg-badge-success/12 text-badge-success',
            danger: 'border-badge-danger/50 bg-badge-danger/12 text-badge-danger',
        },
    },
    defaultVariants: {
        variant: 'success',
    },
});
const Badge: FC<NodeProps<VariantProps<typeof badgeVariants>>> = ({
    className,
    children,
    variant,
}) => {
    return (
        <div className={cn(badgeVariants({ variant, className }))}>
            {children}
        </div>
    );
};

export default Badge;
