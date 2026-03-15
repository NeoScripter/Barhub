import CardLayout from '@/components/layout/CardLayout';
import AccentHeading from '@/components/ui/AccentHeading';
import Filler from '@/components/ui/Filler';
import { cn } from '@/lib/utils';
import Actions from './partials/Actions';
import ExpoSelector from './partials/ExpoSelector';
import Footer from './partials/Footer';
import Tasks from './partials/Tasks';

const Dashboard = () => {
    return (
        <>
            <div className="spacing flex flex-col">
                <div
                    className={cn(
                        'grid gap-8 sm:gap-16 lg:gap-24 xl:grid-cols-[15rem_1fr_15rem] xl:gap-8 2xl:grid-cols-[19rem_1fr_19rem]',
                    )}
                >
                    <Filler className="hidden xl:block" />
                    <div className="spacing flex flex-col">
                        <article>
                            <AccentHeading
                                asChild
                                className="heading text-center text-base text-secondary"
                            >
                                <h1>Активная выставка</h1>
                            </AccentHeading>

                            <CardLayout className="padding w-full sm:gap-6 lg:gap-8">
                                <ExpoSelector />
                            </CardLayout>
                        </article>
                        <div>
                            <AccentHeading
                                asChild
                                className="heading text-center text-base text-secondary"
                            >
                                <h2>Дашборд задач</h2>
                            </AccentHeading>

                            <Tasks />
                        </div>
                    </div>

                    <article>
                        <AccentHeading
                            asChild
                            className="heading text-center text-base text-secondary"
                        >
                            <h2>Быстрые действия</h2>
                        </AccentHeading>

                        <Actions />
                    </article>
                </div>

                <footer>
                    <CardLayout className="padding mx-auto w-full max-w-177.5 items-center justify-evenly sm:flex-row sm:items-baseline">
                        <Footer />
                    </CardLayout>
                </footer>
            </div>
        </>
    );
};

export default Dashboard;
