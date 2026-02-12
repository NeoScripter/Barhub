import CardLayout from '@/components/layout/CardLayout';
import AccentHeading from '@/components/ui/AccentHeading';
import { cn } from '@/lib/utils';
import { App } from '@/wayfinder/types';
import { FC } from 'react';

const Exhibitions: FC<{
    expos: Pick<App.Models.Exhibition, 'name' | 'id'>[];
}> = ({ expos }) => {
    return (
        <div className="flex h-full flex-1 flex-col items-center justify-center gap-4">
            <AccentHeading asChild>
                <h1>Выставки</h1>
            </AccentHeading>
            <CardLayout className="p-6">
                <nav>
                    <ul
                        className={cn(
                            'grid max-h-100 place-content-center gap-6 overflow-y-auto text-foreground',
                        )}
                    >
                        {expos.map((expo) => (
                            <li
                                key={expo.id}
                                className="underline underline-offset-4"
                            >
                                {expo.name}
                            </li>
                        ))}
                    </ul>
                </nav>
            </CardLayout>
        </div>
    );
};

export default Exhibitions;
