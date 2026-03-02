import AccentHeading from '@/components/ui/AccentHeading';
import { Button } from '@/components/ui/Button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/Dialog';
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
                <DialogTitle>Добавить экспонента</DialogTitle>
                <DialogDescription>
                    Добавление экспонента
                </DialogDescription>
                <DialogTrigger asChild>
                    <Button
                        variant="secondary"
                        data-test="edit-exponents"
                    >
                        Добавить
                    </Button>
                </DialogTrigger>
            </div>
            <DialogContent className="h-max max-w-95">
                <div>
                    <AccentHeading className="text-base! text-secondary">
                        Выбрать экспонента
                    </AccentHeading>
                    <CreateExponent />
                </div>
            </DialogContent>
        </Dialog>
    );
};

export default ExponentDialog;
