import InputError from '@/components/form/InputError';
import AccentHeading from '@/components/ui/AccentHeading';
import { Button } from '@/components/ui/Button';
import CopyLinkBtn from '@/components/ui/CopyLinkBtn';
import { DeleteAlertDialog } from '@/components/ui/DeleteAlertDialog';
import { Input } from '@/components/ui/Input';
import { Label } from '@/components/ui/Label';
import RadioCheckbox from '@/components/ui/RadioCheckbox';
import { Spinner } from '@/components/ui/Spinner';
import CompanyLayout from '@/layouts/app/CompanyLayout';
import { index } from '@/wayfinder/routes/admin/companies';
import { Inertia } from '@/wayfinder/types';
import { Link, router, useForm } from '@inertiajs/react';
import { Trash2 } from 'lucide-react';
import { FC, useState } from 'react';
import { toast } from 'sonner';
import { TagSelect } from './partials/TagSelect';

const Edit: FC<Inertia.Pages.Admin.Companies.Edit> = ({ company, tags }) => {
    const { data, setData, post, processing, errors } = useForm({
        _method: 'PUT',
        public_name: company.public_name,
        legal_name: company.legal_name,
        stand_code: String(company.stand_code),
        stand_area: company.stand_area,
        show_on_site: !!company.show_on_site,
        power_kw: company.power_kw,
        storage_enabled: !!company.storage_enabled,
        tags:
            company.tags?.map((t: { id: number }) => t.id) ?? ([] as number[]),
    });

    const [isDeleting, setIsDeleting] = useState(false);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(`/admin/companies/${company.id}`, {
            onSuccess: () => toast.success('Компания успешно обновлена'),
        });
    };

    const handleDelete = () => {
        setIsDeleting(true);
        router.delete(`/admin/companies/${company.id}`, {
            onSuccess: () => toast.success('Компания успешно удалена'),
            onError: () => {
                toast.error('Ошибка при удалении компании');
                setIsDeleting(false);
            },
        });
    };

    return (
        <CompanyLayout>
            <div className="mx-auto w-full max-w-250">
                <div className="mb-8 flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
                    <CopyLinkBtn
                        url={`${window.location.origin}/companies/${company.id}`}
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
                        <div className="grid gap-2">
                            <Label htmlFor="public_name">
                                Публичное название
                            </Label>
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
                            <Label htmlFor="legal_name">
                                Юридическое название
                            </Label>
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

                        <div className="grid gap-2">
                            <Label htmlFor="stand_code">Номер стенда</Label>
                            <Input
                                id="stand_code"
                                type="text"
                                required
                                value={data.stand_code}
                                onChange={(e) =>
                                    setData('stand_code', e.target.value)
                                }
                            />
                            <InputError message={errors.stand_code} />
                        </div>
                        <div className="grid gap-2">
                            <Label htmlFor="power_kw">
                                Электричество (кВт)
                            </Label>
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
                                onChange={(val) =>
                                    setData('storage_enabled', val)
                                }
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
                            Сохранить
                        </Button>
                        <Link href={index().url}>Отмена</Link>
                    </div>
                </form>
            </div>
        </CompanyLayout>
    );
};

export default Edit;
