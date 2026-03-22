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
import { FC, useState } from 'react';
import CreateFollowup from './CreateFollowup';
import { Hash } from 'lucide-react';
import { App } from '@/wayfinder/types';

const FollowupDialog: FC<{service: App.Models.Service | null}> = ({service}) => {

    return (
        <Dialog
            open={service != null}
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
                    <CreateFollowup />
                </div>
            </DialogContent>
        </Dialog>
    );
};

export default FollowupDialog;
