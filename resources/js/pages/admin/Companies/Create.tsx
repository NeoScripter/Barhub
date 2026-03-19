import InputError from '@/components/form/InputError';
import AccentHeading from '@/components/ui/AccentHeading';
import { Button } from '@/components/ui/Button';
import { Input } from '@/components/ui/Input';
import { Label } from '@/components/ui/Label';
import RadioCheckbox from '@/components/ui/RadioCheckbox';
import { Spinner } from '@/components/ui/Spinner';
import { index, store } from '@/wayfinder/routes/admin/companies';
import { Inertia } from '@/wayfinder/types';
import { Link, useForm } from '@inertiajs/react';
import { FC } from 'react';
import { toast } from 'sonner';
import { TagSelect } from './partials/TagSelect';

const Create: FC<Inertia.Pages.Admin.Companies.Create> = ({ tags }) => {
    const { data, setData, post, processing, errors } = useForm({
        public_name: '',
        legal_name: '',
        stand_code: '',
        power_kw: '',
        stand_area: '',
        show_on_site: false,
        storage_enabled: false,
        tags: [] as number[],
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(store().url, {
            onSuccess: () => toast.success('Компания успешно создана'),
        });
    };

    return (
        <div className="mx-auto w-full max-w-250">
            <div className="mb-8 text-center md:mb-12">
                <AccentHeading
                    asChild
                    className="mb-1 text-lg text-secondary"
                >
                    <h2>Создать компанию</h2>
                </AccentHeading>
            </div>
            <form
                onSubmit={handleSubmit}
                className="flex flex-col gap-6"
            >
                <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div className="grid gap-2">
                        <Label htmlFor="public_name">Публичное название</Label>
                        <Input
                            id="public_name"
                            type="text"
                            required
                            value={data.public_name}
                            onChange={(e) =>
                                setData('public_name', e.target.value)
                            }
                            placeholder="Название для отображения на сайте"
                        />
                        <InputError message={errors.public_name} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="legal_name">Юридическое название</Label>
                        <Input
                            id="legal_name"
                            type="text"
                            required
                            value={data.legal_name}
                            onChange={(e) =>
                                setData('legal_name', e.target.value)
                            }
                            placeholder="ООО «Название»"
                        />
                        <InputError message={errors.legal_name} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="stand_code">Номер стенда</Label>
                        <Input
                            id="stand_code"
                            type="text"
                            required
                            min={1}
                            value={data.stand_code}
                            onChange={(e) =>
                                setData('stand_code', e.target.value)
                            }
                        />
                        <InputError message={errors.stand_code} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="power_kw">Электричество (кВт)</Label>
                        <Input
                            id="power_kw"
                            type="text"
                            required
                            min={1}
                            value={data.power_kw}
                            onChange={(e) =>
                                setData('power_kw', e.target.value)
                            }
                        />
                        <InputError message={errors.power_kw} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="stand_area">Площадь стенда</Label>
                        <Input
                            id="stand_area"
                            type="number"
                            required
                            value={data.stand_area}
                            onChange={(e) =>
                                setData('stand_area', e.target.value)
                            }
                        />
                        <InputError message={errors.stand_area} />
                    </div>

                    {tags.length > 0 && (
                        <div className="grid gap-2 md:col-span-2">
                            <Label htmlFor="tags">Теги</Label>
                            <TagSelect
                                availableTags={tags}
                                selectedTagIds={data.tags}
                                onChange={(tags) => setData('tags', tags)}
                            />
                            <InputError message={errors.tags} />
                        </div>
                    )}

                    <div className="md:col-span-2">
                        <RadioCheckbox
                            label="Склад (да/нет)"
                            value={data.storage_enabled}
                            onChange={(val) => setData('storage_enabled', val)}
                        />
                    </div>
                    <div className="md:col-span-2">
                        <RadioCheckbox
                            label="Показывать на сайте"
                            value={data.show_on_site}
                            onChange={(val) => setData('show_on_site', val)}
                        />
                    </div>
                </div>

                <div className="mt-4 flex items-center gap-4">
                    <Button
                        type="submit"
                        className="w-fit"
                        disabled={processing}
                    >
                        {processing && <Spinner />}
                        Создать
                    </Button>
                    <Button
                        variant="tertiary"
                        className="w-fit rounded-md!"
                        asChild
                    >
                        <Link href={index().url}>Отмена</Link>
                    </Button>
                </div>
            </form>
        </div>
    );
};

export default Create;
