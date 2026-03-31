import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/Tooltip';

import { destroy } from '@/wayfinder/routes/admin/themes';
import { App } from '@/wayfinder/types';
import { Link, usePage } from '@inertiajs/react';
import { X } from 'lucide-react';
import { FC } from 'react';
import { toast } from 'sonner';

const DeleteTheme = () => {
    const { themes } = usePage<{ themes: App.Models.Theme[] }>().props;
    return (
        <ul className="my-5 flex flex-wrap gap-2">
            {themes.map((theme) => (
                <li
                    key={theme.id}
                    className="flex items-center gap-2 rounded-md px-3 py-1.5 text-sm text-foreground"
                    style={{ backgroundColor: theme.color_hex }}
                >
                    <span>{theme.name}</span>
                    {theme.events_count > 0 ? (
                        <DisabledDeleteBtn />
                    ) : (
                        <ActiveDeleteBtn theme={theme} />
                    )}
                </li>
            ))}
        </ul>
    );
};

export default DeleteTheme;

const DisabledDeleteBtn = () => {
    return (
        <TooltipProvider delayDuration={0}>
            <Tooltip>
                <TooltipTrigger>
                    <X className="h-4 w-4" />
                </TooltipTrigger>
                <TooltipContent>
                    Данное направление используется в событиях, поэтому ее
                    нельзя удалить
                </TooltipContent>
            </Tooltip>
        </TooltipProvider>
    );
};

const ActiveDeleteBtn: FC<{ theme: App.Models.Theme }> = ({ theme }) => {
    return (
        <Link
            className="hover:opacity-70"
            href={destroy(theme)}
            onSuccess={() => toast.success('Направление удалено')}
        >
            <X className="h-4 w-4" />
        </Link>
    );
};
