import FormButtons from '@/components/form/FormButtons';
import AccentHeading from '@/components/ui/AccentHeading';
import LabeledContent from '@/components/ui/LabeledContent';
import RadioCheckbox from '@/components/ui/RadioCheckbox';
import { formatDateAndTime } from '@/lib/utils';
import { Inertia } from '@/wayfinder/types';
import { useForm } from '@inertiajs/react';
import { FC } from 'react';

const Edit: FC<Inertia.Pages.Admin.Tasks.Edit> = ({ exhibition, task }) => {
    const { data, setData, put, processing } = useForm({
        is_accepted: true,
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        // put(update({ exhibition, task }).url, {
        //     onSuccess: () => {
        //         toast.success('Задача успешно закрыта');
        //     },
        // });
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
                            <li key={comment.id}>
                                <small className="mb-1">
                                    {formatDateAndTime(
                                        new Date(comment.created_at)!,
                                    )}
                                </small>
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
                <RadioCheckbox
                    value={data.is_accepted}
                    onChange={(val) => setData('is_accepted', val)}
                />
                <FormButtons
                    label="Сохранить"
                    processing={processing}
                    backUrl={'/'}
                />
            </form>
        </div>
    );
};

export default Edit;
