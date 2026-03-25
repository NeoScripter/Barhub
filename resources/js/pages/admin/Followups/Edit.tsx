import AccentHeading from '@/components/ui/AccentHeading';
import { Button } from '@/components/ui/Button';
import LabeledContent from '@/components/ui/LabeledContent';
import { Spinner } from '@/components/ui/Spinner';
import { cn, formatDateAndTime } from '@/lib/utils';
import FollowupController from '@/wayfinder/App/Http/Controllers/Admin/FollowupController';
import { destroy, update } from '@/wayfinder/routes/admin/followups';
import { Inertia } from '@/wayfinder/types';
import { Link, router } from '@inertiajs/react';
import { FC, useState } from 'react';
import { toast } from 'sonner';

const Edit: FC<Inertia.Pages.Admin.Followups.Edit> = ({ followup }) => {
    const [processing, setProcessing] = useState(false);

    const handleAcceptClick = () => {
        router.patch(
            update({ followup: followup.id }).url,
            {},
            {
                onStart: () => setProcessing(true),
                onFinish: () => setProcessing(false),
                onSuccess: () => {
                    toast.success('Услуга успешно подтверждена');
                },
            },
        );
    };

    const handleRejectClick = () => {
        router.delete(destroy({ followup: followup.id }).url, {
            onStart: () => setProcessing(true),
            onFinish: () => setProcessing(false),
            onSuccess: () => {
                toast.success('Услуга успешно отклонена');
            },
        });
    };

    return (
        <div className="mx-auto w-full max-w-250">
            <div className="mb-8 text-center md:mb-12">
                <AccentHeading
                    asChild
                    className="mb-1 text-lg text-secondary"
                >
                    <h2>Подтвердить получение услуги</h2>
                </AccentHeading>
            </div>

            <div className="mb-6 flex flex-col gap-6">
                <LabeledContent label="Название компании">
                    <p>{followup.company?.public_name}</p>
                </LabeledContent>
                <LabeledContent label="Название услуги">
                    <p>{followup.name}</p>
                </LabeledContent>
                <LabeledContent label="Описание услуги">
                    <p className="whitespace-pre-line">
                        {followup.description}
                    </p>
                </LabeledContent>
                <LabeledContent label="Комментарий экспонента">
                    {followup.user && (
                        <small className="mb-2 block">
                            {followup.user?.name}
                        </small>
                    )}
                    <small className="mb-2 block">
                        {formatDateAndTime(new Date(followup.created_at)!)}
                    </small>

                    <p>{followup.comment}</p>
                </LabeledContent>
            </div>
            <div className="flex flex-col gap-6">
                <div className={cn('mt-2 flex flex-wrap items-center gap-4')}>
                    <Button
                        className="w-fit"
                        onClick={handleAcceptClick}
                        disabled={processing}
                    >
                        {processing && <Spinner />}
                        Подтвердить
                    </Button>
                    <Button
                        className="w-fit"
                        variant="destructive"
                        onClick={handleRejectClick}
                        disabled={processing}
                    >
                        {processing && <Spinner />}
                        Отклонить
                    </Button>
                    <Button
                        type="button"
                        variant="tertiary"
                        className="w-fit rounded-md!"
                        asChild
                    >
                        <Link href={FollowupController.index().url}>
                            Отмена
                        </Link>
                    </Button>
                </div>
            </div>
        </div>
    );
};

export default Edit;
