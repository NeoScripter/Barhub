import { destroy } from '@/wayfinder/routes/admin/stages';
import { App } from '@/wayfinder/types';
import { Link, usePage } from '@inertiajs/react';
import { X } from 'lucide-react';
import { toast } from 'sonner';

const DeleteStage = () => {
    const { stages } = usePage<{ stages: App.Models.Stage[] }>().props;
    return (
        <div className="my-5 flex flex-wrap gap-2">
            {stages.map((stage) => (
                <div
                    key={stage.id}
                    className="flex items-center gap-2 rounded-md bg-gray-300 px-3 py-1.5 text-sm text-foreground"
                >
                    <span>{stage.name}</span>
                    <Link
                        data-test={`delete stage ${stage.name}`}
                        className="hover:opacity-70"
                        href={destroy(stage.id)}
                        onSuccess={() => toast.success('Площадка удалена')}
                    >
                        <X className="h-4 w-4" />
                    </Link>
                </div>
            ))}
        </div>
    );
};

export default DeleteStage;
