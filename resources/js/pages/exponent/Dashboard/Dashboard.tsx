import AccentHeading from '@/components/ui/AccentHeading';
import { cn } from '@/lib/utils';
import Actions from './partials/Actions';
import Tasks from './partials/Tasks';

const Dashboard = () => {
    return (
        <div className="spacing flex flex-col">
            <AccentHeading
                asChild
                className="heading text-center text-base"
            >
                <h1>Название компании</h1>
            </AccentHeading>

            <article>
                <AccentHeading
                    asChild
                    className={cn(
                        'heading text-center text-base text-foreground md:text-left',
                    )}
                >
                    <h2>Ближайшие задачи</h2>
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

export default Dashboard;
