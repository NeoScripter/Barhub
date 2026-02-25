import * as AlertDialogPrimitive from '@radix-ui/react-alert-dialog';
import { ReactNode } from 'react';
import { Button } from './Button';

type DeleteAlertDialogProps = {
    trigger: ReactNode;
    title: string;
    description: string;
    onConfirm: () => void;
    confirmText?: string;
    cancelText?: string;
    isLoading?: boolean;
};

export function DeleteAlertDialog({
    trigger,
    title,
    description,
    onConfirm,
    confirmText = 'Удалить',
    cancelText = 'Отмена',
    isLoading = false,
}: DeleteAlertDialogProps) {
    return (
        <AlertDialogPrimitive.Root>
            <AlertDialogPrimitive.Trigger asChild>
                {trigger}
            </AlertDialogPrimitive.Trigger>
            <AlertDialogPrimitive.Portal>
                <AlertDialogPrimitive.Overlay className="fixed inset-0 bg-black/50 data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:animate-in data-[state=open]:fade-in-0" />
                <AlertDialogPrimitive.Content className="fixed top-1/2 left-1/2 z-50 w-full max-w-lg -translate-x-1/2 -translate-y-1/2 rounded-lg border bg-white p-6 shadow-lg duration-200 data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=closed]:zoom-out-95 data-[state=closed]:slide-out-to-left-1/2 data-[state=closed]:slide-out-to-top-[48%] data-[state=open]:animate-in data-[state=open]:fade-in-0 data-[state=open]:zoom-in-95 data-[state=open]:slide-in-from-left-1/2 data-[state=open]:slide-in-from-top-[48%] sm:max-w-md">
                    <AlertDialogPrimitive.Title className="text-lg font-semibold">
                        {title}
                    </AlertDialogPrimitive.Title>
                    <AlertDialogPrimitive.Description className="mt-2 text-sm text-muted-foreground">
                        {description}
                    </AlertDialogPrimitive.Description>
                    <div className="mt-6 flex justify-end gap-3">
                        <AlertDialogPrimitive.Cancel asChild>
                            <Button
                                variant="tertiary"
                                type="button"
                                className='rounded-md!'
                            >
                                {cancelText}
                            </Button>
                        </AlertDialogPrimitive.Cancel>
                        <AlertDialogPrimitive.Action asChild>
                            <Button
                                variant="destructive"
                                type="button"
                                onClick={onConfirm}
                                disabled={isLoading}
                            >
                                {isLoading ? 'Удаление...' : confirmText}
                            </Button>
                        </AlertDialogPrimitive.Action>
                    </div>
                </AlertDialogPrimitive.Content>
            </AlertDialogPrimitive.Portal>
        </AlertDialogPrimitive.Root>
    );
}
