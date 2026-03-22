import CardLayout from '@/components/layout/CardLayout';
import { Button } from '@/components/ui/Button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/Dialog';
import { cn } from '@/lib/utils';
import { App } from '@/wayfinder/types';
import { VisuallyHidden } from '@radix-ui/react-visually-hidden';
import { FC, useState } from 'react';
import CreateFollowup from './CreateFollowup';

const ServiceCard: FC<{
    service: App.Models.Service;
    className?: string;
}> = ({ service, className }) => {
    const [isOpen, setIsOpen] = useState(false);

    return (
        <li>
            <Dialog
                open={isOpen}
                onOpenChange={setIsOpen}
            >
                <VisuallyHidden>
                    <DialogTitle>Создать запрос на услугу</DialogTitle>
                    <DialogDescription>{service.placeholder}</DialogDescription>
                </VisuallyHidden>
                <CardLayout
                    className={cn(
                        'relative w-full border-2 border-transparent p-7.5 text-foreground sm:py-9 md:px-8 lg:px-10 lg:py-11',
                        className,
                    )}
                >
                    <DialogTrigger asChild>
                        <Button
                            variant="ghost"
                            className="absolute inset-0 h-full bg-transparent opacity-0"
                        ></Button>
                    </DialogTrigger>

                    <h3 className="mb-3 text-center text-lg font-bold text-balance sm:mb-3 sm:text-xl md:mb-4 lg:mb-5">
                        {service.name}
                    </h3>
                    <p className="text-sm sm:text-base">
                        {service.description}
                    </p>
                </CardLayout>
                <DialogContent className="h-max max-w-95">
                    <div>
                        <CreateFollowup
                            onSuccess={() => setIsOpen(false)}
                            service={service}
                        />
                    </div>
                </DialogContent>
            </Dialog>
        </li>
    );
};

export default ServiceCard;
