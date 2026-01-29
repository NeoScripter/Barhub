import { Button } from '@/components/ui/Button';
import { NodeProps } from '@/types/ui';
import { Plus, Trash, Trash2 } from 'lucide-react';
import { FC } from 'react';

const Home: FC<NodeProps> = ({ className }) => {
    return <div>Привет мир

        <Button  variant="muted">
            {/* <Trash2 /> */}
            This is a button
        </Button>
    </div>;
};

export default Home;
