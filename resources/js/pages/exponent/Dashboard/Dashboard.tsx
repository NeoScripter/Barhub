import AccentHeading from '@/components/ui/AccentHeading';
import { cn } from '@/lib/utils';
import Actions from './partials/Actions';
import Tasks from './partials/Tasks';

const headingStyles = 'mb-4 text-center text-base sm:mb-8 lg:mb-12 2xl:mb-10';
const spacing = 'flex flex-col gap-8 sm:gap-16 lg:gap-24';

const Dashboard = () => {
    return (
        <div className={spacing}>
            <AccentHeading
                asChild
                className={headingStyles}
            >
                <h1>Название компании</h1>
            </AccentHeading>

            <article>
                <AccentHeading
                    asChild
                    className={cn(
                        headingStyles,
                        'text-center text-foreground md:text-left',
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
                        headingStyles,
                        'text-center text-foreground md:text-left',
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
