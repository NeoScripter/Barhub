import FormButtons from '@/components/form/FormButtons';
import AccentHeading from '@/components/ui/AccentHeading';
import DownloadFileLink from '@/components/ui/DownloadFileLink';
import LabeledContent from '@/components/ui/LabeledContent';
import RadioLabeled from '@/components/ui/RadioLabeled';
import { formatDateAndTime } from '@/lib/utils';
import PartnerController from '@/wayfinder/App/Http/Controllers/Admin/PartnerController';
import { update } from '@/wayfinder/routes/admin/exhibitions/all-tasks';
import { Inertia } from '@/wayfinder/types';
import { useForm } from '@inertiajs/react';
import { FC } from 'react';
import { toast } from 'sonner';

const Edit: FC<Inertia.Pages.Admin.Tasks.Edit> = ({ exhibition, task }) => {
    const { data, setData, patch, processing } = useForm({
        is_accepted: true,
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        patch(update({ exhibition, all_task: task.id }).url, {
            onSuccess: () => {
                const message = data.is_accepted
                    ? 'Задача успешно закрыта'
                    : 'Задача отправлена на доработку';
                toast.success(message);
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
                    <h2>Принять/отклонить задачу</h2>
                </AccentHeading>
            </div>

            <div className="mb-6 flex flex-col gap-6">
                <LabeledContent label="Название компании">
                    <p>{task.company?.public_name}</p>
                </LabeledContent>
                <LabeledContent label="Название задачи">
                    <p>{task.title}</p>
                </LabeledContent>
                <LabeledContent label="Дедлайн">
                    <p>{formatDateAndTime(new Date(task.deadline))}</p>
                </LabeledContent>
                <LabeledContent label="Описание задачи">
                    <p>{task.description}</p>
                </LabeledContent>
                <LabeledContent label="История комментариев">
                    <ul className="space-y-5">
                        {task.comments?.map((comment) => (
                            <li
                                key={comment.id}
                                className="space-y-1"
                            >
                                <small className="block">
                                    {comment.user?.name}
                                </small>
                                <small className="block">
                                    {formatDateAndTime(
                                        new Date(comment.created_at)!,
                                    )}
                                </small>
                                {comment.file && (
                                    <span className="block">
                                        <DownloadFileLink
                                            href={comment.file?.url}
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            filename={comment.file?.name}
                                            className="my-4"
                                        />
                                    </span>
                                )}
                                <p>{comment.content}</p>
                            </li>
                        ))}
                    </ul>
                </LabeledContent>
            </div>
            <form
                className="flex flex-col gap-6"
                onSubmit={handleSubmit}
            >
                <RadioLabeled
                    value={data.is_accepted}
                    onChange={(val) => setData('is_accepted', val)}
                />
                <FormButtons
                    label="Сохранить"
                    processing={processing}
                    backUrl={PartnerController.index({ exhibition }).url}
                />
            </form>
        </div>
    );
};

export default Edit;
