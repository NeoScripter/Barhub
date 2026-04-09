import FormButtons from '@/components/form/FormButtons';
import InputError from '@/components/form/InputError';
import AccentHeading from '@/components/ui/AccentHeading';
import FileInput from '@/components/ui/FileInput';
import ImgInput from '@/components/ui/ImgInput';
import { Input } from '@/components/ui/Input';
import { Label } from '@/components/ui/Label';
import { Textarea } from '@/components/ui/Textarea';
import { index, store } from '@/wayfinder/routes/admin/info-items';
import { Inertia } from '@/wayfinder/types';
import { useForm } from '@inertiajs/react';
import { FC } from 'react';
import { toast } from 'sonner';

const Create: FC<Inertia.Pages.Admin.InfoItems.Create> = () => {
    const { data, setData, post, processing, errors } = useForm({
        title: '',
        url: '',
        image: null as File | null,
        description: '',
        file_url: null as File | null,
        file_name: '',
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(store().url, {
            onSuccess: () => toast.success('Элемент успешно создан'),
        });
    };

    return (
        <div className="mx-auto w-full max-w-250">
            <div className="mb-8 text-center md:mb-12">
                <AccentHeading
                    asChild
                    className="mb-1 text-lg text-secondary"
                >
                    <h2>Создать информационный элемент</h2>
                </AccentHeading>
            </div>
            <form
                onSubmit={handleSubmit}
                className="flex flex-col gap-6"
                encType="multipart/form-data"
            >
                <div className="grid gap-6">
                    <div className="grid gap-2">
                        <Label htmlFor="title">Название</Label>
                        <Input
                            id="title"
                            type="text"
                            name="title"
                            value={data.title}
                            onChange={(e) => setData('title', e.target.value)}
                            placeholder="Введите название"
                        />
                        <InputError message={errors.title} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="url">Ссылка</Label>
                        <Input
                            id="url"
                            type="text"
                            name="url"
                            value={data.url}
                            onChange={(e) => setData('url', e.target.value)}
                            placeholder="https://example.com"
                        />
                        <InputError message={errors.url} />
                    </div>
                    <div className="grid gap-2">
                        <Label htmlFor="description">Описание</Label>
                        <Textarea
                            id="description"
                            name="description"
                            value={data.description}
                            onChange={(e) =>
                                setData('description', e.target.value)
                            }
                            className="max-w-full"
                        />
                        <InputError message={errors.description} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="file_url">Прикрепить файл</Label>
                        <FileInput
                            isEdited={true}
                            id="file_url"
                            error={errors.file_url}
                            onChange={(file) => {
                                setData('file_url', file);
                                if (file) setData('file_name', file.name);
                            }}
                        />
                        <InputError message={errors.file_url} />
                    </div>

                    <div className="grid gap-2">
                        <ImgInput
                            isEdited={true}
                            error={errors.image}
                            label='Изображение'
                            onChange={(file) => setData('image', file)}
                        />

                        <InputError message={errors.image} />
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
