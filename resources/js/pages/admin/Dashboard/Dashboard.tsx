import AccentHeading from '@/components/ui/AccentHeading';
import { cn } from '@/lib/utils';
import { App } from '@/wayfinder/types';
import { FC } from 'react';
import ExpoSelector from './partials/ExpoSelector';

const Dashboard: FC<{ expos: App.Models.Exhibition[] }> = ({ expos }) => {
    const headingStyles = 'mb-4 text-center text-secondary text-base';
    return (
        <>
            <div className="flex flex-col gap-8">
                <div className="gap-inherit flex flex-col">
                    <article>
                        <AccentHeading className={cn('', headingStyles)}>
                            Активная выставка
                        </AccentHeading>

                        <ExpoSelector />
                    </article>
                </div>

                <article></article>
            </div>
        </>
    );
};

export default Dashboard;
