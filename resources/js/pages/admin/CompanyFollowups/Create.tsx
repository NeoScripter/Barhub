import FormButtons from '@/components/form/FormButtons';
import InputError from '@/components/form/InputError';
import AccentHeading from '@/components/ui/AccentHeading';
import { Input } from '@/components/ui/Input';
import { Label } from '@/components/ui/Label';
import { Textarea } from '@/components/ui/Textarea';
import { store, index } from '@/wayfinder/App/Http/Controllers/Admin/CompanyFollowupController';
import { Inertia } from '@/wayfinder/types';
import { router, useForm } from '@inertiajs/react';
import { FC } from 'react';
import { toast } from 'sonner';

const Create: FC<Inertia.Pages.Admin.Tasks.Create> = ({
    company,
}) => {
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        description: '',
        comment: '',
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(store({ company }).url, {
            onSuccess: () => {
                router.visit(index({ company }));
                toast.success('Заявка на услугу успешно создана');
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
                    <h2>Создать заявку на услугу</h2>
                </AccentHeading>
            </div>
            <form
                onSubmit={handleSubmit}
                className="flex flex-col gap-6"
            >
                <div className="grid gap-6">
                    <div className="grid gap-2 md:col-span-2">
                        <Label htmlFor="name">Название</Label>
                        <Input
                            id="name"
                            type="text"
                            required
                            name="name"
                            value={data.name}
                            onChange={(e) => setData('name', e.target.value)}
                            placeholder="Введите название заявки на услугу"
                        />
                        <InputError message={errors.name} />
                    </div>

                    <div className="grid gap-2 md:col-span-2">
                        <Label htmlFor="description">Описание</Label>
                        <Textarea
                            id="description"
                            name="description"
                            value={data.description}
                            onChange={(e) =>
                                setData('description', e.target.value)
                            }
                            className="max-w-full"
                            placeholder="Введите описание заявки на услугу"
                        />
                        <InputError message={errors.description} />
                    </div>

                    <div className="grid gap-2 md:col-span-2">
                        <Label htmlFor="comment">Комментарий</Label>
                        <Textarea
                            id="comment"
                            name="comment"
                            value={data.comment}
                            onChange={(e) =>
                                setData('comment', e.target.value)
                            }
                            className="max-w-full"
                            placeholder="Введите комментарий"
                        />
                        <InputError message={errors.comment} />
                    </div>
                </div>

                <FormButtons
                    label="Создать"
                    processing={processing}
                    backUrl={index({ company }).url}
                />
            </form>
        </div>
    );
};

export default Create;
