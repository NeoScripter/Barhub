import AccentHeading from '@/components/ui/AccentHeading';
import { Button } from '@/components/ui/Button';
import { DeleteAlertDialog } from '@/components/ui/DeleteAlertDialog';
import RadioCheckbox from '@/components/ui/RadioCheckbox';
import IntergrationController from '@/wayfinder/App/Http/Controllers/Admin/IntergrationController';
import { router, useForm } from '@inertiajs/react';
import { FC } from 'react';
import { toast } from 'sonner';

const Index: FC<{ output: string[]; status: boolean }> = ({
    output,
    status,
}) => {
    const { data, setData, put } = useForm({
        on: !!status,
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        put(IntergrationController.update(1).url, {
            preserveScroll: false,
            onSuccess: () => {
                toast.success(
                    data.on ? 'Интеграция включена' : 'Интеграция отключена',
                );
            },
        });
    };

    const handleSync = () => {
        router.post(
            IntergrationController.store().url,
            {},
            {
                preserveScroll: false,
                onSuccess: () => {
                    toast.success('Синхронизация началась');
                },
            },
        );
    };

    return (
        <div className="space-y-8 md:space-y-12">
            <div className="">
                <AccentHeading
                    asChild
                    className="mb-1 text-lg"
                >
                    <h2>Мониторинг интеграции</h2>
                </AccentHeading>
            </div>

            <form
                onSubmit={handleSubmit}
                className="space-y-8 md:col-span-2 md:space-y-12"
            >
                <RadioCheckbox
                    label="Статус интеграции"
                    value={data.on}
                    onChange={(val) => setData('on', val)}
                />

                <div className="flex flex-wrap items-center gap-4 md:gap-8">
                    <Button size="lg">Обновить</Button>
                    <DeleteAlertDialog
                        trigger={
                            <Button
                                size="lg"
                                variant="secondary"
                                type="button"
                            >
                                Синхронизация
                            </Button>
                        }
                        title="Провести синхронизацию?"
                        description={
                            'Вы точно уверены, что хотите провести синхронизацию? Остановить данное действие будет невозможно и оно может занять несколько минут, приведя к замедленной работе сайта.'
                        }
                        onConfirm={handleSync}
                        confirmText="Синхронизировать"
                        cancelText="Отмена"
                    />
                </div>
            </form>

            <div>
                <h3 className="mb-3 text-xl font-bold">Логи</h3>
                {output && output.length > 0 ? (
                    <ul className="max-w-250 rounded-md border border-black p-4">
                        {output.map((line, idx) => (
                            <li key={idx}>{line}</li>
                        ))}
                    </ul>
                ) : (
                    <p>Логи пустые</p>
                )}
            </div>
        </div>
    );
};

export default Index;
