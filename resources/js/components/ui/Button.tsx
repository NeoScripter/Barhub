import { Slot } from '@radix-ui/react-slot';
import { cva, type VariantProps } from 'class-variance-authority';
import * as React from 'react';

import { cn } from '@/lib/utils';

const buttonVariants = cva(
    'flex items-center justify-center gap-1 whitespace-nowrap rounded-md text-sm font-medium transition-[color,box-shadow,border-color] disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:stroke-2 cursor-pointer [&_svg]:shrink-0 outline-none focus-visible:border-ring focus-visible:ring-blue-600 focus-visible:ring-[3px] aria-invalid:ring-destructive/20 aria-invalid:border-destructive',
    {
        variants: {
            variant: {
                default: 'bg-primary text-white shadow-xs hover:bg-primary/90',
                destructive:
                    'text-destructive bg-white shadow-xs border border-destructive hover:bg-destructive/90 hover:text-white focus-visible:text-white focus-visible:ring-destructive/20',
                outline:
                    'text-muted bg-white shadow-xs border border-muted hover:bg-muted/90 hover:text-white focus-visible:text-white focus-visible:ring-muted/20',
                secondary:
                    'bg-secondary text-white shadow-xs hover:bg-secondary/90',
                tertiary:
                    'rounded-full! hover:border-primary hover:text-primary border border-foreground text-foreground shadow-xs',
                muted: 'bg-muted text-white shadow-xs hover:bg-muted/90',
                ghost: 'hover:bg-accent hover:text-accent-foreground',
                link: 'text-white underline-offset-4 hover:underline',
            },
            size: {
                default:
                    'h-10.5 text-sm rounded-lg gap-1.5 px-4.5 has-[>svg]:pr-3.5 [&_svg]:size-5',
                sm: 'h-9.5 text-sm rounded-md gap-1.5 px-3.5 has-[>svg]:pr-2.5 [&_svg]:size-4',
                lg: 'h-11.5 rounded-lg gap-2 px-3 text-sm sm:text-base px-5 has-[>svg]:pr-4 [>svg]:size-3 sm:[>svg]:size-3.5',
                icon: 'size-9',
            },
        },
        defaultVariants: {
            variant: 'default',
            size: 'default',
        },
    },
);

function Button({
    className,
    variant,
    size,
    asChild = false,
    ...props
}: React.ComponentProps<'button'> &
    VariantProps<typeof buttonVariants> & {
        asChild?: boolean;
    }) {
    const Comp = asChild ? Slot : 'button';

    return (
        <Comp
            data-slot="button"
            className={cn(buttonVariants({ variant, size, className }))}
            {...props}
        />
    );
}

export { Button, buttonVariants };
