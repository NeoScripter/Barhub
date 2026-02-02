import * as CheckboxPrimitive from '@radix-ui/react-checkbox';
import { CheckIcon } from 'lucide-react';
import * as React from 'react';
import { cva, type VariantProps } from 'class-variance-authority';
import { cn } from '@/lib/utils';

const checkboxVariants = cva(
    [
        'peer shrink-0 border-foreground rounded-sm size-4 border shadow-xs transition-shadow outline-none',
        'focus-visible:ring-[3px] focus-visible:border-muted focus-visible:ring-muted/50',
        'disabled:cursor-not-allowed disabled:opacity-50',
        'aria-invalid:border-destructive aria-invalid:ring-destructive/20',
    ],
    {
        variants: {
            variant: {
                default: [
                    'data-[state=checked]:border-primary data-[state=checked]:bg-primary data-[state=checked]:text-white',
                ],
                outline: [
                    'data-[state=checked]:border-foreground data-[state=checked]:bg-white data-[state=checked]:text-foreground',
                ],
            },
        },
        defaultVariants: {
            variant: 'default',
        },
    }
);

interface CheckboxProps
    extends React.ComponentProps<typeof CheckboxPrimitive.Root>,
        VariantProps<typeof checkboxVariants> {}

function Checkbox({
    className,
    variant,
    ...props
}: CheckboxProps) {
    return (
        <CheckboxPrimitive.Root
            data-slot="checkbox"
            className={cn(checkboxVariants({ variant }), className)}
            {...props}
        >
            <CheckboxPrimitive.Indicator
                data-slot="checkbox-indicator"
                className="flex items-center justify-center text-current transition-none"
            >
                <CheckIcon className="size-3.5" />
            </CheckboxPrimitive.Indicator>
        </CheckboxPrimitive.Root>
    );
}

export { Checkbox, checkboxVariants };
export type { CheckboxProps };
