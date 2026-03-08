import { cn } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import { Link } from '@inertiajs/react';
import { FC } from 'react';
import { Button } from '../ui/Button';
import { Spinner } from '../ui/Spinner';

const FormButtons: FC<
    NodeProps<{ processing: boolean; backUrl: string; label: string }>
> = ({ className, processing, backUrl, label }) => {
    return (
        <div className={cn('mt-2 flex items-center gap-4', className)}>
            <Button
                type="submit"
                data-test="submit-create-task"
                className="w-fit"
                disabled={processing}
            >
                {processing && <Spinner />}
                {label}
            </Button>
            <Button
                type="button"
                variant="tertiary"
                className="w-fit rounded-md!"
                asChild
            >
                <Link href={backUrl}>Отмена</Link>
            </Button>
        </div>
    );
};

export default FormButtons;
