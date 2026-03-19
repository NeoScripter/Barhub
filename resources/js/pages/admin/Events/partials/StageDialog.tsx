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
import { MapPin } from 'lucide-react';
import { useState } from 'react';
import CreateStage from './CreateStage';
import DeleteStage from './DeleteStage';

const StageDialog = () => {
    const [isOpen, setIsOpen] = useState(false);

    return (
        <Dialog
            open={isOpen}
            onOpenChange={setIsOpen}
        >
            <VisuallyHidden>
                <DialogTitle>Управление площадками</DialogTitle>
                <DialogDescription>
                    Создание и удаление площадок
                </DialogDescription>
            </VisuallyHidden>
            <DialogTrigger asChild>
                <Button
                    variant="ghost"
                    data-test="edit-stages"
                    className="border border-primary"
                >
                    Площадки
                    <MapPin />
                </Button>
            </DialogTrigger>
            <DialogContent className="h-max max-w-95">
                <div>
                    <AccentHeading className="text-base! text-secondary">
                        Добавить площадку
                    </AccentHeading>
                    <CreateStage />
                    <AccentHeading className="text-base! text-secondary">
                        Удалить площадку
                    </AccentHeading>
                    <DeleteStage />
                </div>
            </DialogContent>
        </Dialog>
    );
};

export default StageDialog;
