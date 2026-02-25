import InputError from '@/components/form/InputError';
import { Button } from '@/components/ui/Button';
import ColorPicker from '@/components/ui/ColorPicker';
import { Input } from '@/components/ui/Input';
import { Label } from '@/components/ui/Label';
import { store } from '@/wayfinder/routes/admin/themes';
import { useForm } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import { FC } from 'react';
import { toast } from 'sonner';

const CreateTheme: FC = () => {
    const predefinedColors = [
        '#FFE4E8', // Soft Pink
        '#FFBDD6', // Light Pink
        '#F0B3E3', // Lavender Pink
        '#F2D9FF', // Pale Lavender
        '#E5B1F8', // Light Purple
        '#FFFCCF', // Cream
        '#DDF0FE', // Light Sky
        '#DCECC8', // Pale Mint
        '#D8DBFF', // Periwinkle
        '#CBFFC9', // Mint Green
        '#BDF9F5', // Aqua
        '#B6E2ED', // Powder Blue
        '#ED4B97', // Hot Pink
        '#E4FFA4', // Light Lime
    ];

    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        color_hex: predefinedColors[0],
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(store().url, {
            onSuccess: () => {
                toast.success('Направление создано');
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
                <Label htmlFor="name">Название</Label>
                <Input
                    id="name"
                    type="text"
                    required
                    value={data.name}
                    onChange={(e) => setData('name', e.target.value)}
                    placeholder="Введите название"
                />
                <InputError message={errors.name} />
            </div>

            <div className="grid gap-3">
                <Label htmlFor="color_hex">Цвет</Label>
                <ColorPicker
                    colors={predefinedColors}
                    selectedColor={data.color_hex}
                    onColorChange={(color) => setData('color_hex', color)}
                />
                <InputError message={errors.color_hex} />
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

export default CreateTheme;
