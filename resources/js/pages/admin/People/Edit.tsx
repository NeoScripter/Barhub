import FormButtons from '@/components/form/FormButtons';
import InputError from '@/components/form/InputError';
import AccentHeading from '@/components/ui/AccentHeading';
import { Button } from '@/components/ui/Button';
import CopyLinkBtn from '@/components/ui/CopyLinkBtn';
import { DeleteAlertDialog } from '@/components/ui/DeleteAlertDialog';
import ImgInput from '@/components/ui/ImgInput';
import { Input } from '@/components/ui/Input';
import { Label } from '@/components/ui/Label';
import { Textarea } from '@/components/ui/Textarea';
import {
    destroy,
    index,
    update,
} from '@/wayfinder/routes/admin/people';
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
        logo: null as File | null,
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
            <div className="mb-8 text-center md:mb-12">
                <AccentHeading
                    asChild
                    className="mb-1 text-lg text-secondary"
                >
                    <h2>Редактировать участника</h2>
                </AccentHeading>
            </div>

            <div className="mb-8 flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
                <CopyLinkBtn
                    url={`${window.location.origin}/${exhibition.id}/people/${person.id}`}
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
                        error={errors.avatar}
                    />

                    <ImgInput
                        key="logo-input"
                        progress={progress}
                        label="Логотип"
                        isEdited={true}
                        onChange={(file) => setData('logo', file)}
                        src={person.logo?.webp}
                        error={errors.logo}
                    />
                </div>

                <FormButtons
                    label="Сохранить"
                    processing={processing}
                    backUrl={index({ exhibition }).url}
                />
            </form>
        </div>
    );
};

export default Edit;
