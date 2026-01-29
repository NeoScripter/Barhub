import { Button } from '@/components/ui/Button';
import AppLayout from '@/layouts/app/AppLayout';
import { NodeProps } from '@/types/ui';
import { FC } from 'react';

const Home: FC<NodeProps> = ({ className }) => {
    return (
        <AppLayout>
            Привет мир
            <Button variant="muted">
                {/* <Trash2 /> */}
                This is a button
            </Button>
        </AppLayout>
    );
};

export default Home;
