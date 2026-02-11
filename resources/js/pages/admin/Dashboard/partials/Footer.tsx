import AccentHeading from '@/components/ui/AccentHeading';
import { Button } from '@/components/ui/Button';
import { cn } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import { FC } from 'react';

const Footer: FC<NodeProps> = ({ className }) => {
    return (
        <>
            <AccentHeading
                className={cn(className, 'mb-0!')}
            >
                Статус интеграции
            </AccentHeading>

            <Button
                variant="tertiary"
                size="sm"
            >
                статус интеграции
            </Button>
        </>
    );
};

export default Footer;
