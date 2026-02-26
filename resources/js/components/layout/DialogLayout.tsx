import { cn } from '@/lib/utils';
import { Dialog, DialogBackdrop, DialogPanel } from '@headlessui/react';
import { X } from 'lucide-react';

type DialogLayoutProps = {
    children: React.ReactNode;
    show: boolean;
    onClose: () => void;
    className?: string;
    showBtn?: boolean;
};

export default function DialogLayout({
    show,
    children,
    onClose,
    className,
    showBtn = true,
}: DialogLayoutProps) {
    return (
        <Dialog
            open={show}
            onClose={onClose}
            className={cn(
                'fixed inset-0 z-50 flex items-center justify-center overflow-y-auto text-white outline-none',
                className,
            )}
        >
            <DialogBackdrop
                transition
                className="fixed inset-0 backdrop-blur-sm duration-300 ease-in-out data-[closed]:opacity-0"
            />

            <DialogPanel
                transition
                className={cn(
                    'relative z-50 h-max w-9/10 duration-300 ease-in-out data-[closed]:scale-40 data-[closed]:opacity-0',
                )}
            >
                {showBtn && (
                    <button
                        onClick={onClose}
                        className="absolute top-2 right-2 z-20 flex size-8 cursor-pointer items-center justify-center opacity-50 transition-opacity duration-200 ease-in hover:opacity-100 md:size-12"
                    >
                        <X className="size-10" />
                    </button>
                )}
                {children}
            </DialogPanel>
        </Dialog>
    );
}
