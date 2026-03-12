import FormButtons from '@/components/form/FormButtons';
import InputError from '@/components/form/InputError';
import AccentHeading from '@/components/ui/AccentHeading';
import { Input } from '@/components/ui/Input';
import { Label } from '@/components/ui/Label';
import RadioCheckbox from '@/components/ui/RadioCheckbox';
import { index, store } from '@/wayfinder/routes/admin/exhibitions';
import { Inertia } from '@/wayfinder/types';
import { useForm } from '@inertiajs/react';
import { FC } from 'react';
import { toast } from 'sonner';

const Create: FC<Inertia.Pages.Admin.Exhibitions.Create> = () => {
    const { data, setData, post, processing, errors } = useForm({
        name:               '',
        starts_at:          '',
        ends_at:            '',
        location:           '',
        buildin_folder_url: '',
        is_active:          false,
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(store({}).url, {
            onSuccess: () => toast.success('Выставка успешно создана'),
        });
    };

    return (
        <div className="mx-auto w-full max-w-250">
            <div className="mb-8 text-center md:mb-12">
                <AccentHeading asChild className="mb-1 text-lg text-secondary">
                    <h2>Создать выставку</h2>
                </AccentHeading>
            </div>

            <form onSubmit={handleSubmit} className="flex flex-col gap-6">
                <div className="grid gap-6">
                    <div className="grid gap-2">
                        <Label htmlFor="name">Название</Label>
                        <Input
                            id="name"
                            type="text"
                            value={data.name}
                            onChange={(e) => setData('name', e.target.value)}
                            placeholder="Введите название выставки"
                        />
                        <InputError message={errors.name} />
                    </div>

                    <div className="grid gap-6">
                        <div className="grid gap-2">
                            <Label htmlFor="starts_at">Дата начала</Label>
                            <Input
                                id="starts_at"
                                type="date"
                                value={data.starts_at}
                                onChange={(e) => setData('starts_at', e.target.value)}
                            />
                            <InputError message={errors.starts_at} />
                        </div>

                        <div className="grid gap-2">
                            <Label htmlFor="ends_at">Дата окончания</Label>
                            <Input
                                id="ends_at"
                                type="date"
                                value={data.ends_at}
                                onChange={(e) => setData('ends_at', e.target.value)}
                            />
                            <InputError message={errors.ends_at} />
                        </div>
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="location">Место проведения</Label>
                        <Input
                            id="location"
                            type="text"
                            value={data.location}
                            onChange={(e) => setData('location', e.target.value)}
                            placeholder="Введите место проведения"
                        />
                        <InputError message={errors.location} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="buildin_folder_url">Ссылка на информацию и материалы</Label>
                        <Input
                            id="buildin_folder_url"
                            type="url"
                            value={data.buildin_folder_url}
                            onChange={(e) => setData('buildin_folder_url', e.target.value)}
                            placeholder="https://example.com/folder"
                        />
                        <InputError message={errors.buildin_folder_url} />
                    </div>

                    <div>
                        <RadioCheckbox
                            label='Статус публикации'
                            value={data.is_active}
                            onChange={(val) => setData('is_active', val)}
                        />
                        <InputError message={errors.is_active} />
                    </div>
                </div>

                <FormButtons
                    label="Создать"
                    processing={processing}
                    backUrl={index({}).url}
                />
            </form>
        </div>
    );
};

export default Create;
