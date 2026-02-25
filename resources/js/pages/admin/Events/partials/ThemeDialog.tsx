import AccentHeading from '@/components/ui/AccentHeading';
import { Button } from '@/components/ui/Button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/Dialog';
import { Dispatch, FC, SetStateAction } from 'react';
import CreateTheme from './CreateTheme';
import DeleteTheme from './DeleteTheme';

const ThemeDialog: FC<{
    isOpen: boolean;
    setIsOpen: Dispatch<SetStateAction<boolean>>;
}> = ({ isOpen, setIsOpen }) => {
    return (
        <Dialog
            open={isOpen}
            onOpenChange={setIsOpen}
        >
            <div className="space-y-3">
                <DialogTitle>Управление направлениями</DialogTitle>
                <DialogDescription>
                    Создание и удаление направлений
                </DialogDescription>
                <DialogTrigger asChild>
                    <Button
                        variant="secondary"
                        className="sm:ml-auto"
                    >
                        Редактировать
                    </Button>
                </DialogTrigger>
            </div>
            <DialogContent className="h-max max-w-95">
                <div>
                    <AccentHeading className="text-base! text-secondary">
                        Добавить направление
                    </AccentHeading>
                    <CreateTheme />
                    <AccentHeading className="text-base! text-secondary">
                        Удалить направление
                    </AccentHeading>
                    <DeleteTheme />
                </div>
            </DialogContent>
        </Dialog>
    );
};

export default ThemeDialog;
