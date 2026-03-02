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
import CreateExponent from './CreateExponent';

const ExponentDialog = () => {
    const [isOpen, setIsOpen] = useState(false);

    return (
        <Dialog
            open={isOpen}
            onOpenChange={setIsOpen}
        >
            <div className="space-y-3">
                <VisuallyHidden>
                    <DialogTitle>Добавить экспонента</DialogTitle>
                    <DialogDescription>Добавление экспонента</DialogDescription>
                </VisuallyHidden>
                <DialogTrigger
                    className="mx-auto"
                    asChild
                >
                    <Button
                        variant="secondary"
                        data-test="edit-exponents"
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
                    <CreateExponent />
                </div>
            </DialogContent>
        </Dialog>
    );
};

export default ExponentDialog;
