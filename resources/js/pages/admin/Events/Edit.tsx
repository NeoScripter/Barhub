import InputError from '@/components/form/InputError';
import { Button } from '@/components/ui/Button';
import { Input } from '@/components/ui/Input';
import { Label } from '@/components/ui/Label';
import { Spinner } from '@/components/ui/Spinner';
import { Textarea } from '@/components/ui/Textarea';
import { store } from '@/wayfinder/routes/register';
import { Inertia } from '@/wayfinder/types';
import { Form } from '@inertiajs/react';
import { FC } from 'react';

const Edit: FC<Inertia.Pages.Admin.Events.Edit> = ({ event }) => {
    return (
        <div className="mx-auto w-full max-w-250">
            <Form
                {...store()}
                resetOnSuccess={['password', 'password_confirmation']}
                disableWhileProcessing
                className="flex flex-col gap-6"
            >
                {({ processing, errors }) => (
                    <>
                        <div className="grid gap-6">
                            <div className="grid gap-2">
                                <Label htmlFor="title">Названиие</Label>
                                <Input
                                    id="title"
                                    type="text"
                                    required
                                    autoFocus
                                    tabIndex={1}
                                    autoComplete="title"
                                    name="title"
                                    placeholder="Название"
                                    defaultValue={event.title}
                                />
                                <InputError
                                    message={errors.title}
                                    className="mt-2"
                                />
                            </div>
                            <div className="grid gap-2 lg:col-span-2">
                                <Label htmlFor="description">Описание</Label>
                                <Textarea
                                    id="description"
                                    required
                                    autoFocus
                                    tabIndex={1}
                                    autoComplete="description"
                                    name="description"
                                    placeholder="Описание"
                                    defaultValue={event.description}
                                />
                                <InputError
                                    message={errors.description}
                                    className="mt-2"
                                />
                            </div>
                        </div>

                        <div className="grid gap-6 sm:grid-cols-2 max-w-2xl">
                            <div className="grid gap-2">
                                <Label htmlFor="starts_at">Время начала</Label>
                                <Input
                                    id="starts_at"
                                    type="datetime-local"
                                    required
                                    tabIndex={2}
                                    autoComplete="starts_at"
                                    name="starts_at"
                                />
                                <InputError message={errors.email} />
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="ends_at">Время окончания</Label>
                                <Input
                                    id="ends_at"
                                    type="datetime-local"
                                    required
                                    tabIndex={3}
                                    autoComplete="ends_at"
                                    name="ends_at"
                                />
                                <InputError
                                    message={errors.name}
                                    className="mt-2"
                                />
                            </div>
                        </div>
                        <Button
                            type="submit"
                            className="mt-2 w-fit"
                            tabIndex={5}
                            data-test="register-user-button"
                        >
                            {processing && <Spinner />}
                            Создать аккаунт
                        </Button>
                    </>
                )}
            </Form>
        </div>
    );
};

export default Edit;
