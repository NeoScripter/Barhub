import SearchInput from '@/components/ui/SearchInput';
import AppLayout from '@/layouts/app/AdminLayout';

const Home = () => {
    return (
        <AppLayout>
            <div className="ml-auto max-w-100">Привет мир</div>
            <SearchInput
                handleChange={() => {}}
                value="hello"
            />
        </AppLayout>
    );
};

export default Home;
