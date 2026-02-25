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
import CreateStage from './CreateStage';
import DeleteStage from './DeleteStage';

const StageDialog = () => {
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
