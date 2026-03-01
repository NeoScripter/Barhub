import InputError from '@/components/form/InputError';
import { Button } from '@/components/ui/Button';
import CopyLinkBtn from '@/components/ui/CopyLinkBtn';
import { DeleteAlertDialog } from '@/components/ui/DeleteAlertDialog';
import ImgInput from '@/components/ui/ImgInput';
import { Input } from '@/components/ui/Input';
import { Label } from '@/components/ui/Label';
import { Spinner } from '@/components/ui/Spinner';
import { Textarea } from '@/components/ui/Textarea';
import { Inertia } from '@/wayfinder/types';
import { router, useForm } from '@inertiajs/react';
import { Trash2 } from 'lucide-react';
import { FC, useState } from 'react';
import { toast } from 'sonner';
import { TagSelect } from './partials/TagSelect';

const Edit: FC<Inertia.Pages.Admin.Companies.Edit> = ({
    company,
    exhibition,
    tags,
}) => {
    const { data, setData, post, processing, errors, progress } = useForm({
        _method: 'PUT',
        public_name: company.public_name,
        legal_name: company.legal_name,
        description: company.description,
        phone: company.phone,
        email: company.email,
        site_url: company.site_url ?? '',
        instagram: company.instagram ?? '',
        telegram: company.telegram ?? '',
        stand_code: String(company.stand_code),
        show_on_site: company.show_on_site,
        activities: company.activities ?? '',
        tags:
            company.tags?.map((t: { id: number }) => t.id) ?? ([] as number[]),
        logo: null as File | null,
        logo_alt: company.logo?.alt ?? '',
    });

    const [isDeleting, setIsDeleting] = useState(false);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(`/admin/exhibitions/${exhibition.id}/companies/${company.id}`, {
            onSuccess: () => toast.success('Компания успешно обновлена'),
        });
    };

    const handleDelete = () => {
        setIsDeleting(true);
        router.delete(
            `/admin/exhibitions/${exhibition.id}/companies/${company.id}`,
            {
                onSuccess: () => toast.success('Компания успешно удалена'),
                onError: () => {
                    toast.error('Ошибка при удалении компании');
                    setIsDeleting(false);
                },
            },
        );
    };

    return (
        <div className="mx-auto w-full max-w-250">
            <div className="mb-8 flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
                <CopyLinkBtn
                    url={`${window.location.origin}/exhibitions/${exhibition.id}/companies/${company.id}`}
                />

                <DeleteAlertDialog
                    trigger={
                        <Button
                            variant="destructive"
                            type="button"
                        >
                            Удалить компанию
                            <Trash2 />
                        </Button>
                    }
                    title="Удалить компанию?"
                    description={`Вы уверены, что хотите удалить компанию "${company.public_name}"? Это действие нельзя отменить.`}
                    onConfirm={handleDelete}
                    confirmText="Удалить"
                    cancelText="Отмена"
                    isLoading={isDeleting}
                />
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

                    <div className="grid gap-2">
                        <Label htmlFor="stand_code">Номер стенда</Label>
                        <Input
                            id="stand_code"
                            type="number"
                            required
                            min={1}
                            value={data.stand_code}
                            onChange={(e) =>
                                setData('stand_code', e.target.value)
                            }
                        />
                        <InputError message={errors.stand_code} />
                    </div>

                    <div className="grid gap-2 md:col-span-2">
                        <Label htmlFor="activities">Деятельность</Label>
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

                    <div className="flex items-center gap-3 md:col-span-2">
                        <input
                            id="show_on_site"
                            type="checkbox"
                            checked={data.show_on_site}
                            onChange={(e) =>
                                setData('show_on_site', e.target.checked)
                            }
                            className="h-4 w-4 rounded border-gray-300"
                        />
                        <Label htmlFor="show_on_site">
                            Показывать на сайте
                        </Label>
                        <InputError message={errors.show_on_site} />
                    </div>
                    {tags.length > 0 && (
                        <div className="grid max-w-lg gap-2">
                            <Label htmlFor="tags">Теги</Label>
                            <TagSelect
                                availableTags={tags}
                                selectedTagIds={data.tags}
                                onChange={(tags) => setData('tags', tags)}
                            />
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
                    src={company.logo?.webp}
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
