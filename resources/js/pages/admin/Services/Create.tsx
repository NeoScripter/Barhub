import FormButtons from '@/components/form/FormButtons';
import InputError from '@/components/form/InputError';
import AccentHeading from '@/components/ui/AccentHeading';
import { Input } from '@/components/ui/Input';
import { Label } from '@/components/ui/Label';
import RadioCheckbox from '@/components/ui/RadioCheckbox';
import { Textarea } from '@/components/ui/Textarea';
import { index, store } from '@/wayfinder/routes/admin/services';
import { Inertia } from '@/wayfinder/types';
import { router, useForm } from '@inertiajs/react';
import { FC } from 'react';
import { toast } from 'sonner';

const Create: FC<Inertia.Pages.Admin.Tasks.Create> = () => {
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        description: '',
        is_active: true,
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(store().url, {
            onSuccess: () => {
                router.visit(index());
                toast.success('Услуга успешно создана');
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
                    <h2>Создать услугу</h2>
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
                            placeholder="Введите название задачи"
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
                            placeholder="Введите описание задачи"
                        />
                        <InputError message={errors.description} />
                    </div>

                    <div className="md:col-span-2">
                        <RadioCheckbox
                            value={data.is_active}
                            onChange={(val) => setData('is_active', val)}
                        />
                    </div>
                </div>

                <FormButtons
                    label="Создать"
                    processing={processing}
                    backUrl={index().url}
                />
            </form>
        </div>
    );
};

export default Create;
