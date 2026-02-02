import InputError from '@/components/form/InputError';
import { Button } from '@/components/ui/Button';
import { Input } from '@/components/ui/Input';
import { Label } from '@/components/ui/Label';
import { Spinner } from '@/components/ui/Spinner';
import AuthLayout from '@/layouts/auth/AuthLayout';
import { store } from '@/routes/password/confirm';
import { Form, Head } from '@inertiajs/react';

export default function ConfirmPassword() {
    return (
        <AuthLayout
            title="Подтвердите пароль"
            description="Для получения доступа, пожалуйста, подтвердите пароль."
        >
            <Head title="Подтверждение пароля" />

            <Form
                {...store.form()}
                resetOnSuccess={['password']}
            >
                {({ processing, errors }) => (
                    <div className="space-y-6">
                        <div className="grid gap-2">
                            <Label htmlFor="password">Пароль</Label>
                            <Input
                                id="password"
                                type="password"
                                name="password"
                                placeholder="Пароль"
                                autoComplete="current-password"
                                autoFocus
                            />

                            <InputError message={errors.password} />
                        </div>

                        <div className="flex items-center">
                            <Button
                                className="w-full"
                                disabled={processing}
                                data-test="confirm-password-button"
                            >
                                {processing && <Spinner />}
                                Подтвердить пароль
                            </Button>
                        </div>
                    </div>
                )}
            </Form>
        </AuthLayout>
    );
}
