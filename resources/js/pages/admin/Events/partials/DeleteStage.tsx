import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/Tooltip';
import { destroy } from '@/wayfinder/routes/admin/stages';
import { App } from '@/wayfinder/types';
import { Link, usePage } from '@inertiajs/react';
import { X } from 'lucide-react';
import { FC } from 'react';
import { toast } from 'sonner';

const DeleteStage = () => {
    const { stages } = usePage<{ stages: App.Models.Stage[] }>().props;
    return (
        <ul className="my-5 flex flex-wrap gap-2">
            {stages.map((stage) => (
                <li
                    key={stage.id}
                    className="flex items-center gap-2 rounded-md bg-gray-300 px-3 py-1.5 text-sm text-foreground"
                >
                    <span>{stage.name}</span>
                    {stage.events_count > 0 ? (
                        <DisabledDeleteBtn />
                    ) : (
                        <ActiveDeleteBtn stage={stage} />
                    )}
                </li>
            ))}
        </ul>
    );
};

export default DeleteStage;

const DisabledDeleteBtn = () => {
    return (
        <TooltipProvider delayDuration={0}>
            <Tooltip>
                <TooltipTrigger>
                    <X className="h-4 w-4" />
                </TooltipTrigger>
                <TooltipContent>
                    Данная площадка используется в событиях, поэтому ее нельзя
                    удалить
                </TooltipContent>
            </Tooltip>
        </TooltipProvider>
    );
};

const ActiveDeleteBtn: FC<{ stage: App.Models.Stage }> = ({ stage }) => {
    return (
        <Link
            data-test={`delete stage ${stage.name}`}
            className="hover:opacity-70"
            href={destroy(stage.id)}
            onSuccess={() => toast.success('Площадка удалена')}
        >
            <X className="h-4 w-4" />
        </Link>
    );
};
