import CardLayout from '@/components/layout/CardLayout';
import Image from '@/components/ui/Image';
import { cn } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import { show } from '@/wayfinder/routes/companies';
import { App } from '@/wayfinder/types';
import { Link } from '@inertiajs/react';
import { FC } from 'react';

const PartnerCard: FC<
    NodeProps<{
        company: App.Models.Company;
        exhibition: App.Models.Exhibition;
        highlighted?: boolean;
    }>
> = ({ className, exhibition, company, highlighted = false }) => {
    return (
        <li className={cn(highlighted && 'sm:col-span-2 sm:row-span-2')}>
            <CardLayout
                className={cn(
                    'relative w-full p-5 text-foreground aspect-square ring-primary transition-transform hover:scale-103 hover:ring-2',
                    className,
                )}
            >
                {company.logo && (
                    <Image
                        wrapperStyles={cn(
                            'mb-4 aspect-video w-full',
                            highlighted ? 'w-full sm:mb-6' : 'max-w-3/5',
                        )}
                        imgStyles='object-contain'
                        image={company.logo}
                    />
                )}

                <Link
                    href={show({ exhibition, company }).url}
                    className="absolute inset-0"
                />

                <h3
                    className={cn(
                        'mx-auto mb-4 min-h-[1.6em] max-w-4/5 text-center font-bold',
                        highlighted && 'sm:text-2xl',
                    )}
                >
                    {' '}
                    {company.public_name}
                </h3>

                <p
                    className={cn(
                        'min-h-[1.6em] text-center text-sm',
                        highlighted && 'text-lg',
                    )}
                >
                    {company.tags?.map((tag) => tag.name).join(', ')}
                </p>
            </CardLayout>
        </li>
    );
};

export default PartnerCard;
