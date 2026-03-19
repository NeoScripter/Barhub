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
import { Rocket } from 'lucide-react';
import { useState } from 'react';
import CreateTheme from './CreateTheme';
import DeleteTheme from './DeleteTheme';

const ThemeDialog = () => {
    const [isOpen, setIsOpen] = useState(false);

    return (
        <Dialog
            open={isOpen}
            onOpenChange={setIsOpen}
        >
            <VisuallyHidden>
                <DialogTitle>Управление направлениями</DialogTitle>
                <DialogDescription>
                    Создание и удаление направлений
                </DialogDescription>
            </VisuallyHidden>

            <DialogTrigger asChild>
                <Button
                    variant="ghost"
                    className="border border-primary"
                >
                    Направления
                    <Rocket />
                </Button>
            </DialogTrigger>
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
