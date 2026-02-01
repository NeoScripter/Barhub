import Badge from '@/components/ui/Badge';
import { Button } from '@/components/ui/Button';
import AppLayout from '@/layouts/app/AdminLayout';

const HomePage = () => {
    return (
        <AppLayout>
            Привет мир
            <Button
                className="self-start"
                variant="muted"
            >
                {/* <Trash2 /> */}
                This is a button
            </Button>
            <Badge variant='danger'>
                Off
            </Badge>
        </AppLayout>
    );
};

export default HomePage;
