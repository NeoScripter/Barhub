import CardLayout from '@/components/layout/CardLayout';
import AccentHeading from '@/components/ui/AccentHeading';
import { App } from '@/wayfinder/types';
import { FC } from 'react';
import ExpoSelector from './partials/ExpoSelector';
import Footer from './partials/Footer';

const Dashboard: FC<{ expos: App.Models.Exhibition[] }> = ({ expos }) => {
    const headingStyles = 'mb-4 text-center text-secondary text-base';
    return (
        <>
            <div className="flex flex-col gap-8">
                <div className="gap-inherit flex flex-col">
                    <article>
                        <AccentHeading className={headingStyles}>
                            Активная выставка
                        </AccentHeading>

                        <CardLayout className="w-full space-y-4 px-12 py-8">
                            <ExpoSelector />
                        </CardLayout>
                    </article>
                </div>

                <article></article>

                <footer>
                    <CardLayout className="w-full items-center justify-evenly gap-4 px-12 py-8 sm:flex-row sm:items-baseline">
                        <Footer className={headingStyles} />
                    </CardLayout>
                </footer>
            </div>
        </>
    );
};

export default Dashboard;
