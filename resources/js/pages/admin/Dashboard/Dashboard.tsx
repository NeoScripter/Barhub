import CardLayout from '@/components/layout/CardLayout';
import AccentHeading from '@/components/ui/AccentHeading';
import { cn } from '@/lib/utils';
import Actions from './partials/Actions';
import ExpoSelector from './partials/ExpoSelector';
import Footer from './partials/Footer';
import Tasks from './partials/Tasks';

const headingStyles =
    'mb-4 text-center text-secondary text-base sm:mb-8 lg:mb-12 2xl:mb-10';
const padding = 'px-12 gap-4 py-8';
const spacing = 'flex flex-col gap-8 sm:gap-16 lg:gap-24 2xl:gap-8';

const Dashboard = () => {
    return (
        <>
            <div className={spacing}>
                <div
                    className={cn(
                        'grid gap-8 sm:gap-16 lg:gap-24 2xl:grid-cols-[19rem_1fr_19rem] 2xl:gap-8',
                    )}
                >
                    <article className="">
                        <AccentHeading className={headingStyles}>
                            Активная выставка
                        </AccentHeading>

                        <CardLayout
                            className={cn('w-full sm:gap-6 lg:gap-8', padding)}
                        >
                            <ExpoSelector />
                        </CardLayout>
                    </article>
                    <article className="2xl:-order-2">
                        <AccentHeading className={headingStyles}>
                            Дашборд задач
                        </AccentHeading>

                        <Tasks />
                    </article>

                    <article>
                        <AccentHeading className={headingStyles}>
                            Быстрые действия
                        </AccentHeading>

                        <Actions />
                    </article>
                </div>

                <footer>
                    <CardLayout
                        className={cn(
                            'mx-auto w-full max-w-177.5 items-center justify-evenly sm:flex-row sm:items-baseline',
                            padding,
                        )}
                    >
                        <Footer className={headingStyles} />
                    </CardLayout>
                </footer>
            </div>
        </>
    );
};

export default Dashboard;
