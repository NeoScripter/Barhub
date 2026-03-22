import CardLayout from '@/components/layout/CardLayout';
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/Tooltip';
import { cn } from '@/lib/utils';
import { App } from '@/wayfinder/types';
import { FC } from 'react';

const ServiceCard: FC<{
    service: App.Models.Service;
    className?: string;
}> = ({ service, className }) => {
    return (
        <li>
            <TooltipProvider delayDuration={0}>
                <CardLayout
                    className={cn(
                        'relative w-full border-2 border-transparent p-7.5 text-foreground sm:py-9 md:px-8 lg:px-10 lg:py-11',
                        className,
                    )}
                >
                    {/* <button type='button' */}
                    {/*     className="absolute top-4 right-4 flex items-center justify-center p-1" */}
                    {/* > */}
                    {/*     button */}
                    {/* </button> */}
                    <h3 className="mb-3 text-center text-lg font-bold text-balance sm:mb-3 sm:text-xl md:mb-4 lg:mb-5">
                        {service.name}
                    </h3>
                    <Tooltip>
                        <TooltipTrigger>
                            <p className="text-sm sm:text-base">
                                {service.description}
                            </p>
                        </TooltipTrigger>
                        <TooltipContent
                            side="right"
                            align="end"
                            className="p-4 text-xs text-foreground"
                        >
                            <p>{service.placeholder}</p>
                        </TooltipContent>
                    </Tooltip>
                </CardLayout>
            </TooltipProvider>
        </li>
    );
};

export default ServiceCard;
