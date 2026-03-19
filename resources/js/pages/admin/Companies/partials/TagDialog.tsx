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
import { useState } from 'react';
import CreateTag from './CreateTag';
import DeleteTag from './DeleteTag';
import { Hash } from 'lucide-react';

const TagDialog = () => {
    const [isOpen, setIsOpen] = useState(false);

    return (
        <Dialog
            open={isOpen}
            onOpenChange={setIsOpen}
        >
            <VisuallyHidden>
                <DialogTitle>Управление тегами</DialogTitle>
                <DialogDescription>Создание и удаление тегов</DialogDescription>
            </VisuallyHidden>

            <DialogTrigger asChild>
                <Button
                    variant="ghost"
                    data-test="edit-tags"
                    className="border border-primary"
                >
                    Тэги
                    <Hash />
                </Button>
            </DialogTrigger>
            <DialogContent className="h-max max-w-95">
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
