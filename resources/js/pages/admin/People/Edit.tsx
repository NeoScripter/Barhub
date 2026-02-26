import InputError from '@/components/form/InputError';
import { Button } from '@/components/ui/Button';
import CopyLinkBtn from '@/components/ui/CopyLinkBtn';
import { DeleteAlertDialog } from '@/components/ui/DeleteAlertDialog';
import ImgInput from '@/components/ui/ImgInput';
import { Input } from '@/components/ui/Input';
import { Label } from '@/components/ui/Label';
import { Spinner } from '@/components/ui/Spinner';
import { Textarea } from '@/components/ui/Textarea';
import { destroy, update } from '@/wayfinder/routes/admin/exhibitions/people';
import { Inertia } from '@/wayfinder/types';
import { router, useForm } from '@inertiajs/react';
import { Trash2 } from 'lucide-react';
import { FC, useState } from 'react';
import { toast } from 'sonner';

const Edit: FC<Inertia.Pages.Admin.People.Edit> = ({ person, exhibition }) => {
    const { data, setData, post, processing, errors, progress } = useForm({
        _method: 'PUT',
        name: person.name,
        regalia: person.regalia,
        bio: person.bio,
        telegram: person.telegram || '',
        avatar: null as File | null,
        avatar_alt: person.avatar?.alt || '',
        logo: null as File | null,
        logo_alt: person.logo?.alt || '',
    });

    const [isDeleting, setIsDeleting] = useState(false);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(update({ person, exhibition }).url, {
            onSuccess: () => toast.success('Участник успешно обновлен'),
        });
    };

    const handleDelete = () => {
        setIsDeleting(true);
        router.delete(destroy({ person, exhibition }).url, {
            onSuccess: () => {
                toast.success('Участник успешно удален');
            },
            onError: () => {
                toast.error('Ошибка при удалении участника');
                setIsDeleting(false);
            },
        });
    };

    return (
        <div className="mx-auto w-full max-w-250">
            <div className="mb-8 flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
                <CopyLinkBtn
                    url={`${window.location.origin}/exhibitions/${exhibition.slug}/people/${person.id}`}
                />

                <DeleteAlertDialog
                    trigger={
                        <Button
                            variant="destructive"
                            type="button"
                        >
                            Удалить участника
                            <Trash2 />
                        </Button>
                    }
                    title="Удалить участника?"
                    description={`Вы уверены, что хотите удалить участника "${person.name}"? Это действие нельзя отменить.`}
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
                <div className="grid gap-6">
                    <div className="grid gap-2">
                        <Label htmlFor="name">Имя</Label>
                        <Input
                            id="name"
                            type="text"
                            required
                            value={data.name}
                            onChange={(e) => setData('name', e.target.value)}
                        />
                        <InputError message={errors.name} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="regalia">Регалии</Label>
                        <Textarea
                            id="regalia"
                            required
                            value={data.regalia}
                            onChange={(e) => setData('regalia', e.target.value)}
                            className="max-w-full"
                        />
                        <InputError message={errors.regalia} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="bio">Биография</Label>
                        <Textarea
                            id="bio"
                            required
                            value={data.bio}
                            onChange={(e) => setData('bio', e.target.value)}
                            className="max-w-full"
                        />
                        <InputError message={errors.bio} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="telegram">
                            Telegram (необязательно)
                        </Label>
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
                </div>

                <div className="grid gap-6">
                    <ImgInput
                        key="avatar-input"
                        label="Аватар"
                        progress={progress}
                        isEdited={true}
                        onChange={(file) => setData('avatar', file)}
                        src={person.avatar?.webp}
                        onAltChange={(val) => setData('avatar_alt', val)}
                        altError={errors.avatar_alt}
                        altText={data.avatar_alt}
                        error={errors.avatar}
                    />

                    <ImgInput
                        key="logo-input"
                        progress={progress}
                        label="Логотип"
                        isEdited={true}
                        onChange={(file) => setData('logo', file)}
                        src={person.logo?.webp}
                        onAltChange={(val) => setData('logo_alt', val)}
                        altError={errors.logo_alt}
                        altText={data.logo_alt}
                        error={errors.logo}
                    />
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
