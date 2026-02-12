import CardLayout from '@/components/layout/CardLayout';
import AccentHeading from '@/components/ui/AccentHeading';
import { cn } from '@/lib/utils';
import EventController from '@/wayfinder/App/Http/Controllers/User/EventController';
import { App } from '@/wayfinder/types';
import { Link } from '@inertiajs/react';
import { FC } from 'react';

const Exhibitions: FC<{
    expos: Pick<App.Models.Exhibition, 'name' | 'id' | 'slug'>[];
}> = ({ expos }) => {
    return (
        <div className="flex h-full flex-1 flex-col items-center justify-center gap-4">
            <AccentHeading asChild>
                <h1>Выставки</h1>
            </AccentHeading>
            <CardLayout className="w-full max-w-80 p-6">
                <nav className="w-full">
                    <ul
                        className={cn(
                            'grid max-h-100 w-full gap-6 overflow-y-auto text-foreground',
                        )}
                    >
                        {expos.map((expo) => (
                            <li
                                key={expo.id}
                                className="underline underline-offset-4"
                            >
                                <Link
                                    href={EventController.index({
                                        exhibition: expo.slug,
                                    })}
                                    className="hover:text-primary"
                                >
                                    {expo.name}
                                </Link>
                            </li>
                        ))}
                    </ul>
                </nav>
            </CardLayout>
        </div>
    );
};

export default Exhibitions;
