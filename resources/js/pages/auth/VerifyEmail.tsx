import TextLink from '@/components/form/TextLink';
import { Button } from '@/components/ui/Button';
import { Spinner } from '@/components/ui/Spinner';
import AuthLayout from '@/layouts/auth/AuthLayout';
import { logout } from '@/routes';
import { send } from '@/routes/verification';
import { Form, Head } from '@inertiajs/react';

export default function VerifyEmail({ status }: { status?: string }) {
    return (
        <AuthLayout
            title="Подтверждение электронной почты"
            description="Пожалуйста, подтвердите ваш адрес электронной почты, перейдя по ссылке, которую мы только что отправили вам."
        >
            <Head title="Подтверждение электронной почты" />

            {status === 'verification-link-sent' && (
                <div className="mb-4 text-center text-sm font-medium text-green-600">
                    Новая ссылка для подтверждения была отправлена на адрес
                    электронной почты, который вы указали при регистрации.
                </div>
            )}

            <Form
                {...send.form()}
                className="space-y-6 text-center"
            >
                {({ processing }) => (
                    <>
                        <Button
                            disabled={processing}
                            variant="secondary"
                        >
                            {processing && <Spinner />}
                            Отправить ссылку для подтверждения снова
                        </Button>

                        <TextLink
                            href={logout()}
                            className="mx-auto block text-sm"
                        >
                            Выйти
                        </TextLink>
                    </>
                )}
            </Form>
        </AuthLayout>
    );
}
