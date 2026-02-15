import { cn } from '@/lib/utils';
import { Slot } from '@radix-ui/react-slot';

function AccentHeading({
    className,
    asChild = false,
    ...props
}: React.ComponentProps<'p'> & {
    asChild?: boolean;
}) {
    const Comp = asChild ? Slot : 'p';

    return (
        <Comp
            data-slot="p"
            className={cn(
                'text-xs font-bold text-primary uppercase sm:text-xl 2xl:text-2xl',
                className,
            )}
            {...props}
        />
    );
}

export default AccentHeading;
