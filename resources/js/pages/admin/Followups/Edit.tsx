import FormButtons from '@/components/form/FormButtons';
import AccentHeading from '@/components/ui/AccentHeading';
import LabeledContent from '@/components/ui/LabeledContent';
import { formatDateAndTime } from '@/lib/utils';
import FollowupController from '@/wayfinder/App/Http/Controllers/Admin/FollowupController';
import { update } from '@/wayfinder/routes/admin/followups';
import { Inertia } from '@/wayfinder/types';
import { useForm } from '@inertiajs/react';
import { FC } from 'react';
import { toast } from 'sonner';

const Edit: FC<Inertia.Pages.Admin.Followups.Edit> = ({

    followup,
}) => {
    const { patch, processing } = useForm();

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        patch(update({ followup: followup.id }).url, {
            onSuccess: () => {
                toast.success('Услуга успешно подтверждена');
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
                    <p>{followup.description}</p>
                </LabeledContent>
                <LabeledContent label="Комментарий экспонента">
                    {followup.user && (
                        <small className="mb-2 block">
                            {followup.user?.name}
                        </small>
                    )}
                    <small className="block mb-2">
                        {formatDateAndTime(new Date(followup.created_at)!)}
                    </small>

                    <p>{followup.comment}</p>
                </LabeledContent>
            </div>
            <form
                className="flex flex-col gap-6"
                onSubmit={handleSubmit}
            >
                <FormButtons
                    label="Подтвердить"
                    processing={processing}
                    backUrl={FollowupController.index({ exhibition }).url}
                />
            </form>
        </div>
    );
};

export default Edit;
