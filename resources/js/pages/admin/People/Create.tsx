import FormButtons from '@/components/form/FormButtons';
import InputError from '@/components/form/InputError';
import AccentHeading from '@/components/ui/AccentHeading';
import ImgInput from '@/components/ui/ImgInput';
import { Input } from '@/components/ui/Input';
import { Label } from '@/components/ui/Label';
import { Textarea } from '@/components/ui/Textarea';
import { index, store } from '@/wayfinder/routes/admin/exhibitions/people';
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
        logo: null as File | null,
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(store({ exhibition }).url, {
            onSuccess: () => toast.success('Участник успешно создан'),
        });
    };

    return (
        <div className="mx-auto w-full max-w-250">
            <div className="mb-8 text-center md:mb-12">
                <AccentHeading
                    asChild
                    className="mb-1 text-lg text-secondary"
                >
                    <h2>Создать участника</h2>
                </AccentHeading>
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
                        error={errors.avatar}
                    />

                    <ImgInput
                        key="logo-input"
                        progress={progress}
                        label="Логотип"
                        isEdited={true}
                        onChange={(file) => setData('logo', file)}
                        error={errors.logo}
                    />
                </div>

                <FormButtons
                    label="Создать"
                    processing={processing}
                    backUrl={index({ exhibition }).url}
                />
            </form>
        </div>
    );
};

export default Create;
