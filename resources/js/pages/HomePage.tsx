import ActionCard, {
    ActionCardBtn,
    ActionCardIcon,
    ActionCardTitle,
} from '@/components/ui/ActionCard';
import Badge from '@/components/ui/Badge';
import { Button } from '@/components/ui/Button';
import AppLayout from '@/layouts/app/AdminLayout';
import { Briefcase } from 'lucide-react';

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
            <Badge variant="danger">Off</Badge>
            <ActionCard>
                <ActionCardIcon icon={Briefcase} />
                <ActionCardTitle>Спикеры</ActionCardTitle>
                <ActionCardBtn onClick={() => {}}>
                    Добавить событие
                </ActionCardBtn>
            </ActionCard>
        </AppLayout>
    );
};

export default HomePage;
