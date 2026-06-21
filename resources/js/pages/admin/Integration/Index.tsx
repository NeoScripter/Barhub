import AccentHeading from '@/components/ui/AccentHeading';
import { Button } from '@/components/ui/Button';
import RadioCheckbox from '@/components/ui/RadioCheckbox';
import { FC, useState } from 'react';

const Index: FC<{ output: string[] }> = ({ output }) => {
    const [on, setOn] = useState(false);

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

            <div className="md:col-span-2">
                <RadioCheckbox
                    label="Статус публикации"
                    value={on}
                    onChange={(val) => setOn(val)}
                />
            </div>

            <div className="flex flex-wrap items-center gap-4 md:gap-8">
                <Button size="lg">Обновить</Button>
                <Button
                    size="lg"
                    variant="secondary"
                >
                    Синхронизация
                </Button>
            </div>

            <div>
                <h3 className="mb-3 text-xl font-bold">Логи</h3>
                <ul className="max-w-250 rounded-md border border-black p-4">
                    {output.map((line, idx) => (
                        <li key={idx}>{line}</li>
                    ))}
                </ul>
            </div>
        </div>
    );
};

export default Index;
