import { Button } from '@/components/ui/Button';
import AppLayout from '@/layouts/app/AdminLayout';

const Home = () => {
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
        </AppLayout>
    );
};

export default Home;
