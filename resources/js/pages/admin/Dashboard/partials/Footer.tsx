import AccentHeading from '@/components/ui/AccentHeading';
import { Button } from '@/components/ui/Button';
import IntergrationController from '@/wayfinder/App/Http/Controllers/Admin/IntergrationController';
import { Link } from '@inertiajs/react';

const Footer = () => {
    return (
        <>
            <AccentHeading className="heading mb-0! text-center text-base text-secondary">
                Статус интеграции
            </AccentHeading>

            <Button
                variant="tertiary"
                size="sm"
                asChild
            >
                <Link href={IntergrationController.index()}>
                    статус интеграции
                </Link>
            </Button>
        </>
    );
};

export default Footer;
