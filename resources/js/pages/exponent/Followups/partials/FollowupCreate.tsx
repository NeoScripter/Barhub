import InputError from '@/components/form/InputError';
import { Button } from '@/components/ui/Button';
import { Input } from '@/components/ui/Input';
import { Label } from '@/components/ui/Label';
import { store } from '@/wayfinder/routes/admin/followups';
import { useForm } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import { FC } from 'react';
import { toast } from 'sonner';

const CreateFollowup: FC = () => {
    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(store().url, {
            onSuccess: () => {
                toast.success('Тег создан');
                reset();
            },
        });
    };

    return (
        <form
            onSubmit={handleSubmit}
            className="my-5 flex flex-col gap-4"
        >
            <div className="grid gap-2">
                <Label htmlFor="followupname">Название</Label>
                <Input
                    id="followupname"
                    type="text"
                    required
                    name='followupname'
                    value={data.name}
                    onChange={(e) => setData('name', e.target.value)}
                    placeholder="Введите название"
                />
                <InputError message={errors.name} />
            </div>

            <Button
                type="submit"
                variant="secondary"
                disabled={processing}
                className="w-fit"
            >
                Добавить
                <Plus />
            </Button>
        </form>
    );
};

export default CreateFollowup;
