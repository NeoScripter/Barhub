import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/Tooltip';

import { destroy } from '@/wayfinder/routes/admin/tags';
import { App } from '@/wayfinder/types';
import { Link, usePage } from '@inertiajs/react';
import { X } from 'lucide-react';
import { FC } from 'react';
import { toast } from 'sonner';

const DeleteTag = () => {
    const { tags } = usePage<{ tags: App.Models.Tag[] }>().props;
    return (
        <ul
            id="tag-list"
            className="my-5 flex flex-wrap gap-2"
        >
            {tags.map((tag) => (
                <li
                    key={tag.id}
                    className="flex items-center gap-2 rounded-md bg-gray-300 px-3 py-1.5 text-sm text-foreground"
                >
                    <span>{tag.name}</span>
                    <ActiveDeleteBtn tag={tag} />
                </li>
            ))}
        </ul>
    );
};

export default DeleteTag;

const DisabledDeleteBtn = () => {
    return (
        <TooltipProvider delayDuration={0}>
            <Tooltip>
                <TooltipTrigger>
                    <X className="h-4 w-4" />
                </TooltipTrigger>
                <TooltipContent>
                    Данная тэг используется в компаниях, поэтому его нельзя
                    удалить
                </TooltipContent>
            </Tooltip>
        </TooltipProvider>
    );
};

const ActiveDeleteBtn: FC<{ tag: App.Models.Tag }> = ({ tag }) => {
    return (
        <Link
            data-test={`delete tag ${tag.name}`}
            className="hover:opacity-70"
            href={destroy(tag.id)}
            onSuccess={() => toast.success('Тег удален')}
        >
            <X className="h-4 w-4" />
        </Link>
    );
};
