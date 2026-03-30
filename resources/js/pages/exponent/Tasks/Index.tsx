import AccentHeading from '@/components/ui/AccentHeading';
import { cn } from '@/lib/utils';
import { usePage } from '@inertiajs/react';
import Actions from './partials/Actions';
import Tasks from './partials/Tasks';

const Index = () => {
    const { company } = usePage<{ company: string }>().props;

    return (
        <div className="spacing flex flex-col">
            <AccentHeading
                asChild
                className="heading text-center text-base"
            >
                <h1>{company}</h1>
            </AccentHeading>

            <article>
                <AccentHeading
                    asChild
                    className={cn(
                        'heading text-center text-base text-foreground md:text-left',
                    )}
                >
                    <h2>Задачи</h2>
                </AccentHeading>
                <Tasks />
            </article>
            <article>
                <AccentHeading
                    asChild
                    className={cn(
                        'heading text-center text-base text-foreground md:text-left',
                    )}
                >
                    <h2>Быстрые действия</h2>
                </AccentHeading>

                <Actions />
            </article>
        </div>
    );
};

export default Index;
