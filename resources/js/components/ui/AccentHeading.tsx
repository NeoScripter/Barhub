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
            className={cn("text-primary text-xs sm:text-xl xl:text-2xl uppercase font-bold",className)}
            {...props}
        />
    );
}

export default AccentHeading;
