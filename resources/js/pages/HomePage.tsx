import ActionCard, {
    ActionCardBtn,
    ActionCardIcon,
    ActionCardTitle,
} from '@/components/ui/ActionCard';
import Badge from '@/components/ui/Badge';
import { Button } from '@/components/ui/Button';
import { SelectMenu } from '@/components/ui/SelectMenu';
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
            <ActionCard className="ml-auto">
                <ActionCardIcon icon={Briefcase} />
                <ActionCardTitle>Спикеры</ActionCardTitle>
                <ActionCardBtn onClick={() => {}}>
                    Добавить событие
                </ActionCardBtn>
                <SelectMenu
                    variant="solid"
                    size="lg"
                    items={['bananas', 'cherry', 'apple']}
                />
            </ActionCard>
            <SelectMenu
                className="ml-auto"
                items={['bananas', 'cherry', 'apple']}
            />
        </AppLayout>
    );
};

export default HomePage;
