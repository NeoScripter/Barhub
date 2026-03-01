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
import CreateTag from './CreateTag';
import DeleteTag from './DeleteTag';

const TagDialog = () => {
    const [isOpen, setIsOpen] = useState(false);

    return (
        <Dialog
            open={isOpen}
            onOpenChange={setIsOpen}
        >
            <div className="space-y-3">
                <DialogTitle>Управление площадками</DialogTitle>
                <DialogDescription>
                    Создание и удаление площадок
                </DialogDescription>
                <DialogTrigger asChild>
                    <Button
                        variant="secondary"
                        data-test="edit-tags"
                    >
                        Редактировать
                    </Button>
                </DialogTrigger>
            </div>
            <DialogContent className="h-max max-w-95">
                <div>
                    <AccentHeading className="text-base! text-secondary">
                        Добавить площадку
                    </AccentHeading>
                    <CreateTag />
                    <AccentHeading className="text-base! text-secondary">
                        Удалить площадку
                    </AccentHeading>
                    <DeleteTag />
                </div>
            </DialogContent>
        </Dialog>
    );
};

export default TagDialog;
