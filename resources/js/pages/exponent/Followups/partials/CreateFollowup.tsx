import InputError from '@/components/form/InputError';
import { Button } from '@/components/ui/Button';
import { Label } from '@/components/ui/Label';
import { Textarea } from '@/components/ui/Textarea';
import { store } from '@/wayfinder/routes/exponent/followups';
import { App } from '@/wayfinder/types';
import { useForm } from '@inertiajs/react';
import { FC } from 'react';
import { toast } from 'sonner';

const CreateFollowup: FC<{ service: App.Models.Service, onSuccess: () => void }> = ({ service, onSuccess }) => {
    const { data, setData, post, processing, errors, reset } = useForm({
        comment: '',
        service_id: service.id
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(store().url, {
            onSuccess: () => {
                toast.success('Запрос на услугу создан');
                onSuccess();
                reset();
            },
        });
    };

    return (
        <div className="mt-12">
            <h4 className="mb-6 text-xl font-bold">{service.name}</h4>
            <p className="mb-8">{service.description}</p>
            <form
                onSubmit={handleSubmit}
                className="my-5 flex flex-col gap-8"
            >
                <div className="grid gap-2">
                    <Label htmlFor="comment">Комментарий</Label>
                    <Textarea
                        id="comment"
                        type="text"
                        required
                        name="comment"
                        value={data.comment}
                        onChange={(e) => setData('comment', e.target.value)}
                        placeholder="Введите комментарий"
                    />
                    <InputError message={errors.comment} />
                </div>

                <Button
                    type="submit"
                    variant="default"
                    disabled={processing}
                    className="mx-auto w-fit"
                >
                    Отправить заявку на услугу
                </Button>
            </form>
        </div>
    );
};

export default CreateFollowup;
