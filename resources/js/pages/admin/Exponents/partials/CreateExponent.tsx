import InputError from '@/components/form/InputError';
import { Button } from '@/components/ui/Button';
import { Input } from '@/components/ui/Input';
import { Label } from '@/components/ui/Label';
import ExponentEmailController from '@/wayfinder/App/Http/Controllers/Admin/ExponentEmailController';
import { Inertia } from '@/wayfinder/types';
import { useForm, usePage } from '@inertiajs/react';
import { toast } from 'sonner';

const CreateExponent = () => {
    const { company } = usePage<Inertia.Pages.Admin.Exponents.Index>().props;

    const { data, setData, post, processing, errors } = useForm({
        email: '',
    });

    const handleClick = () => {
        if (!data.email || data.email.trim().length === 0) return;

        post(ExponentEmailController.store({ company }), {
            method: 'post',
            onSuccess: () => {
                toast.success('Уведомление отправлено на указанный емаил пользователя');
            },
            preserveScroll: true,
        });
    };

    return (
        <div className="flex w-full flex-col gap-4 sm:gap-6 lg:gap-8">
            <div className="grid gap-2">
                <Label htmlFor="email">Email</Label>
                <Input
                    id="email"
                    type="email"
                    value={data.email}
                    onChange={(e) => setData('email', e.target.value)}
                    placeholder="company@example.com"
                />
                <InputError message={errors.email} />
            </div>
            <Button
                onClick={handleClick}
                variant="default"
                size="sm"
                className="mx-auto"
                data-test="add-exponent"
                disabled={processing}
            >
                Создать
            </Button>
        </div>
    );
};

export default CreateExponent;
