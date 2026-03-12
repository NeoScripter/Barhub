import AccentHeading from '@/components/ui/AccentHeading';
import { Button } from '@/components/ui/Button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/Dialog';
import { VisuallyHidden } from '@radix-ui/react-visually-hidden';
import { Plus } from 'lucide-react';
import { useState } from 'react';
import CreateAdmin from './CreateAdmin';

const AdminDialog = () => {
    const [isOpen, setIsOpen] = useState(false);

    return (
        <Dialog
            open={isOpen}
            onOpenChange={setIsOpen}
        >
            <div className="space-y-3">
                <VisuallyHidden>
                    <DialogTitle>Добавить администратора</DialogTitle>
                    <DialogDescription>Добавление администратора</DialogDescription>
                </VisuallyHidden>
                <DialogTrigger
                    className="mx-auto"
                    asChild
                >
                    <Button
                        variant="secondary"
                        data-test="select-admin"
                        size='lg'
                    >
                        Добавить
                        <Plus />
                    </Button>
                </DialogTrigger>
            </div>
            <DialogContent className="h-max max-w-95">
                <div>
                    <AccentHeading className="mb-4 text-lg! text-secondary sm:mb-6 lg:mb-8">
                        Пользователи
                    </AccentHeading>
                    <CreateAdmin />
                </div>
            </DialogContent>
        </Dialog>
    );
};

export default AdminDialog;
