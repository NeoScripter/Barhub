import { destroy } from '@/wayfinder/routes/admin/themes';
import { App } from '@/wayfinder/types';
import { Link, usePage } from '@inertiajs/react';
import { X } from 'lucide-react';
import { toast } from 'sonner';

const DeleteTheme = () => {
    const { themes } = usePage<{ themes: App.Models.Theme[] }>().props;
    return (
        <div className="my-5 flex flex-wrap gap-2">
            {themes.map((theme) => (
                <div
                    key={theme.id}
                    className="flex items-center gap-2 rounded-md px-3 py-1.5 text-sm text-foreground"
                    style={{ backgroundColor: theme.color_hex }}
                >
                    <span>{theme.name}</span>
                    <Link
                        className="hover:opacity-70"
                        href={destroy(theme)}
                        onSuccess={() => toast.success('Направление удалено')}
                    >
                        <X className="h-4 w-4" />
                    </Link>
                </div>
            ))}
        </div>
    );
};

export default DeleteTheme;
