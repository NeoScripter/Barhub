import InputError from '@/components/form/InputError';
import { Button } from '@/components/ui/Button';
import ImgInput from '@/components/ui/ImgInput';
import { Input } from '@/components/ui/Input';
import { Label } from '@/components/ui/Label';
import { Spinner } from '@/components/ui/Spinner';
import { Textarea } from '@/components/ui/Textarea';
import { store } from '@/wayfinder/routes/admin/exhibitions/people';
import { Inertia } from '@/wayfinder/types';
import { useForm } from '@inertiajs/react';
import { FC } from 'react';
import { toast } from 'sonner';

const Create: FC<Inertia.Pages.Admin.People.Create> = ({ exhibition }) => {
    const { data, setData, post, processing, errors, progress } = useForm({
        name: '',
        regalia: '',
        bio: '',
        telegram: '',
        avatar: null as File | null,
        avatar_alt: '',
        logo: null as File | null,
        logo_alt: '',
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(store({ exhibition }).url, {
            onSuccess: () => toast.success('Участник успешно создан'),
        });
    };

    return (
        <div className="mx-auto w-full max-w-250">
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
                            placeholder="Введите имя участника"
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
                            placeholder="Введите регалии участника"
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
                            placeholder="Введите биографию участника"
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
