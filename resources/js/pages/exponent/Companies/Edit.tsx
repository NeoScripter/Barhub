import InputError from '@/components/form/InputError';
import AccentHeading from '@/components/ui/AccentHeading';
import { Button } from '@/components/ui/Button';
import ImgInput from '@/components/ui/ImgInput';
import { Input } from '@/components/ui/Input';
import { Label } from '@/components/ui/Label';
import { Spinner } from '@/components/ui/Spinner';
import { Textarea } from '@/components/ui/Textarea';
import { update } from '@/wayfinder/routes/exponent/companies';
import { Inertia } from '@/wayfinder/types';
import { useForm } from '@inertiajs/react';
import { FC } from 'react';
import { toast } from 'sonner';

const Edit: FC<Inertia.Pages.Admin.Companies.Edit> = ({ company }) => {
    const { data, setData, post, processing, errors, progress } = useForm({
        _method: 'PUT',
        public_name: company.public_name ?? '',
        legal_name: company.legal_name ?? '',
        description: company.description ?? '',
        phone: company.phone ?? '',
        email: company.email ?? '',
        site_url: company.site_url ?? '',
        instagram: company.instagram ?? '',
        telegram: company.telegram ?? '',
        activities: company.activities ?? '',
        logo: null as File | null,
    });

    console.log(company);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(update({ company: company.id }).url, {
            onSuccess: () => toast.success('Данные компании успешно обновлены'),
        });
    };

    return (
        <div className="mx-auto w-full max-w-250">
            <div className="mb-8 text-center md:mb-12">
                <AccentHeading
                    asChild
                    className="mb-1 text-lg text-secondary"
                >
                    <h2>Редактировать компанию</h2>
                </AccentHeading>
            </div>

            <form
                onSubmit={handleSubmit}
                className="flex flex-col gap-6"
            >
                <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <ImgInput
                        key="logo-input"
                        progress={progress}
                        label="Логотип"
                        isEdited={true}
                        onChange={(file) => setData('logo', file)}
                        src={company.logo?.webp}
                        error={errors.logo}
                    />

                    <div className="grid gap-2 md:col-span-2">
                        <Label htmlFor="public_name">Публичное название</Label>
                        <Input
                            id="public_name"
                            type="text"
                            required
                            value={data.public_name}
                            onChange={(e) =>
                                setData('public_name', e.target.value)
                            }
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
                        />
                        <InputError message={errors.legal_name} />
                    </div>

                    <div className="grid gap-2 md:col-span-2">
                        <Label htmlFor="description">Описание</Label>
                        <Textarea
                            id="description"
                            required
                            value={data.description}
                            onChange={(e) =>
                                setData('description', e.target.value)
                            }
                            className="max-w-full"
                        />
                        <InputError message={errors.description} />
                    </div>

                    <AccentHeading className="text-lg text-secondary md:col-span-2">
                        Контакты на сайте
                    </AccentHeading>

                    <div className="grid gap-2">
                        <Label htmlFor="phone">Телефон</Label>
                        <Input
                            id="phone"
                            type="text"
                            required
                            value={data.phone}
                            onChange={(e) => setData('phone', e.target.value)}
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
                        />
                        <InputError message={errors.email} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="site_url">Сайт</Label>
                        <Input
                            id="site_url"
                            type="url"
                            value={data.site_url}
                            onChange={(e) =>
                                setData('site_url', e.target.value)
                            }
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
                            onChange={(e) =>
                                setData('instagram', e.target.value)
                            }
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
                            onChange={(e) =>
                                setData('telegram', e.target.value)
                            }
                            placeholder="@username"
                        />
                        <InputError message={errors.telegram} />
                    </div>

                    <div className="grid gap-2 md:col-span-2">
                        <Label htmlFor="activities">Активности на стенде</Label>
                        <Textarea
                            id="activities"
                            value={data.activities}
                            onChange={(e) =>
                                setData('activities', e.target.value)
                            }
                            className="max-w-full"
                        />
                        <InputError message={errors.activities} />
                    </div>
                </div>

                <div className="mt-4 flex items-center gap-4">
                    <Button
                        type="submit"
                        className="w-fit"
                        disabled={processing}
                    >
                        {processing && <Spinner />}
                        Сохранить
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

export default Edit;
