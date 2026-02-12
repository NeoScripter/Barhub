import NavItem from '@/components/layout/AppHeader/partials/NavItem';
import CardLayout from '@/components/layout/CardLayout';
import AccentHeading from '@/components/ui/AccentHeading';
import { useCurrentUrl } from '@/hooks/useCurrentUrl';
import { renderNavItems } from '@/lib/data/navItems';
import { cn } from '@/lib/utils';

const Home = () => {
    const { currentUrl } = useCurrentUrl();
    return (
        <div className="flex h-full flex-1 flex-col items-center justify-center gap-4">
            <AccentHeading asChild>
                <h1>Публичные страницы</h1>
            </AccentHeading>
            <CardLayout className="p-6">
                <nav>
                    <ul className={cn('grid place-content-center gap-6')}>
                        {renderNavItems(currentUrl).map((item) => (
                            <NavItem
                                expanded={true}
                                key={item.id}
                                item={item}
                            />
                        ))}
                    </ul>
                </nav>
            </CardLayout>
        </div>
    );
};

export default Home;
