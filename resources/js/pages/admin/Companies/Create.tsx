import InputError from '@/components/form/InputError';
import { Button } from '@/components/ui/Button';
import ImgInput from '@/components/ui/ImgInput';
import { Input } from '@/components/ui/Input';
import { Label } from '@/components/ui/Label';
import { Spinner } from '@/components/ui/Spinner';
import { Textarea } from '@/components/ui/Textarea';
import { Inertia } from '@/wayfinder/types';
import { useForm } from '@inertiajs/react';
import { FC } from 'react';
import { toast } from 'sonner';

const Create: FC<Inertia.Pages.Admin.Companies.Create> = ({
    exhibition,
    tags,
}) => {
    const { data, setData, post, processing, errors, progress } = useForm({
        public_name:  '',
        legal_name:   '',
        description:  '',
        phone:        '',
        email:        '',
        site_url:     '',
        instagram:    '',
        telegram:     '',
        stand_code:   '',
        show_on_site: false,
        activities:   '',
        tags:         [] as number[],
        logo:         null as File | null,
        logo_alt:     '',
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(`/admin/exhibitions/${exhibition.id}/companies`, {
            onSuccess: () => toast.success('Компания успешно создана'),
        });
    };

    const toggleTag = (id: number) => {
        setData(
            'tags',
            data.tags.includes(id)
                ? data.tags.filter((t) => t !== id)
                : [...data.tags, id],
        );
    };

    return (
        <div className="mx-auto w-full max-w-250">
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
                            onChange={(e) => setData('public_name', e.target.value)}
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
                            onChange={(e) => setData('legal_name', e.target.value)}
                            placeholder="ООО «Название»"
                        />
                        <InputError message={errors.legal_name} />
                    </div>

                    <div className="grid gap-2 md:col-span-2">
                        <Label htmlFor="description">Описание</Label>
                        <Textarea
                            id="description"
                            required
                            value={data.description}
                            onChange={(e) => setData('description', e.target.value)}
                            className="max-w-full"
                            placeholder="Описание компании"
                        />
                        <InputError message={errors.description} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="phone">Телефон</Label>
                        <Input
                            id="phone"
                            type="text"
                            required
                            value={data.phone}
                            onChange={(e) => setData('phone', e.target.value)}
                            placeholder="+7 (999) 000-00-00"
                        />
                        <InputError message={errors.phone} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="email">Email</Label>
                        <Input
                            id="email"
                            type="email"
                            required
                            value={data.email}
                            onChange={(e) => setData('email', e.target.value)}
                            placeholder="company@example.com"
                        />
                        <InputError message={errors.email} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="site_url">Сайт</Label>
                        <Input
                            id="site_url"
                            type="url"
                            value={data.site_url}
                            onChange={(e) => setData('site_url', e.target.value)}
                            placeholder="https://example.com"
                        />
                        <InputError message={errors.site_url} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="instagram">Instagram</Label>
                        <Input
                            id="instagram"
                            type="text"
                            value={data.instagram}
                            onChange={(e) => setData('instagram', e.target.value)}
                            placeholder="@username"
                        />
                        <InputError message={errors.instagram} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="telegram">Telegram</Label>
                        <Input
                            id="telegram"
                            type="text"
                            value={data.telegram}
                            onChange={(e) => setData('telegram', e.target.value)}
                            placeholder="@username"
                        />
                        <InputError message={errors.telegram} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="stand_code">Номер стенда</Label>
                        <Input
                            id="stand_code"
                            type="number"
                            required
                            min={1}
                            value={data.stand_code}
                            onChange={(e) => setData('stand_code', e.target.value)}
                        />
                        <InputError message={errors.stand_code} />
                    </div>

                    <div className="grid gap-2 md:col-span-2">
                        <Label htmlFor="activities">Деятельность</Label>
                        <Textarea
                            id="activities"
                            value={data.activities}
                            onChange={(e) => setData('activities', e.target.value)}
                            className="max-w-full"
                            placeholder="Описание деятельности компании"
                        />
                        <InputError message={errors.activities} />
                    </div>

                    <div className="flex items-center gap-3 md:col-span-2">
                        <input
                            id="show_on_site"
                            type="checkbox"
                            checked={data.show_on_site}
                            onChange={(e) => setData('show_on_site', e.target.checked)}
                            className="h-4 w-4 rounded border-gray-300"
                        />
                        <Label htmlFor="show_on_site">Показывать на сайте</Label>
                        <InputError message={errors.show_on_site} />
                    </div>

                    {tags.length > 0 && (
                        <div className="grid gap-2 md:col-span-2">
                            <Label>Теги</Label>
                            <div className="flex flex-wrap gap-2">
                                {tags.map((tag) => (
                                    <button
                                        key={tag.id}
                                        type="button"
                                        onClick={() => toggleTag(tag.id)}
                                        className={`rounded-full border px-3 py-1 text-sm transition-colors ${
                                            data.tags.includes(tag.id)
                                                ? 'border-blue-500 bg-blue-50 text-blue-700'
                                                : 'border-gray-300 bg-white text-gray-600 hover:border-gray-400'
                                        }`}
                                    >
                                        {tag.name}
                                    </button>
                                ))}
                            </div>
                            <InputError message={errors.tags} />
                        </div>
                    )}
                </div>

                <ImgInput
                    key="logo-input"
                    progress={progress}
                    label="Логотип"
                    isEdited={true}
                    onChange={(file) => setData('logo', file)}
                    onAltChange={(val) => setData('logo_alt', val)}
                    altError={errors.logo_alt}
                    altText={data.logo_alt}
                    error={errors.logo}
                />

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
                        type="button"
                        variant="tertiary"
                        className="w-fit rounded-md!"
                        onClick={() => history.back()}
                    >
                        Отмена
                    </Button>
                </div>
            </form>
        </div>
    );
};

export default Create;
