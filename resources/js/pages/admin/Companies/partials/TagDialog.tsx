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
                <DialogTitle>Управление тегами</DialogTitle>
                <DialogDescription>
                    Создание и удаление тегов
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
            <DialogContent className="h-max max-w-95 data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0">
                <div>
                    <AccentHeading className="text-base! text-secondary">
                        Добавить тег
                    </AccentHeading>
                    <CreateTag />
                    <AccentHeading className="text-base! text-secondary">
                        Удалить тег
                    </AccentHeading>
                    <DeleteTag />
                </div>
            </DialogContent>
        </Dialog>
    );
};

export default TagDialog;
