import CardLayout from '@/components/layout/CardLayout';
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/Tooltip';
import { edit } from '@/wayfinder/routes/admin/exhibitions/services';
import { App, Inertia } from '@/wayfinder/types';
import { Link, usePage } from '@inertiajs/react';
import { Pencil } from 'lucide-react';
import { FC } from 'react';

const ServiceCard: FC<{ service: App.Models.Service }> = ({ service }) => {
    const { exhibition, company } =
        usePage<Inertia.Pages.Admin.Services.Index>().props;

    return (
        <li>
            <TooltipProvider delayDuration={0}>
                <CardLayout className="relative w-full p-7.5 text-foreground sm:py-9 md:px-8 lg:px-10 lg:py-11">
                    <Link
                        href={
                            edit({
                                exhibition,
                                company,
                                service: service.id,
                            }).url
                        }
                        className="absolute top-4 right-4 flex items-center justify-center p-1"
                    >
                        <Pencil className="size-5 opacity-50" />
                    </Link>
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
