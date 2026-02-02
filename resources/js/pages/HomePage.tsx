import TaskCard from '@/components/ui/TaskCard';
import AppLayout from '@/layouts/app/AdminLayout';

const HomePage = () => {
    return (
        <AppLayout>
            <div className="ml-auto max-w-100">Привет мир</div>
            <TaskCard>
                <TaskCard.Badge variant="danger">Urgent</TaskCard.Badge>
                <TaskCard.Digit value={10} />
                <TaskCard.Label>задач</TaskCard.Label>
            </TaskCard>
        </AppLayout>
    );
};

export default HomePage;
