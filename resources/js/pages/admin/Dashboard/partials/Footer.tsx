import AccentHeading from '@/components/ui/AccentHeading';
import { Button } from '@/components/ui/Button';

const Footer = () => {
    return (
        <>
            <AccentHeading className="heading mb-0! text-center text-base text-secondary">
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
