import BurgerMenuIcon from '@/components/ui/BurgerMenuIcon';
import { Dialog, DialogContent, DialogTrigger } from '@/components/ui/Dialog';
import AccountDropdown from './AccountDropdown';
import NavMenu from './NavMenu';

const AccountMenuControls = () => {
    return (
        <>
            <Dialog>
                <DialogTrigger asChild>
                    <button className="mr-2 size-8 sm:size-9 lg:hidden">
                        <BurgerMenuIcon className="size-full" />
                    </button>
                </DialogTrigger>
                <DialogContent
                    overlayStyles="lg:hidden"
                    className="top-0 right-0 left-auto h-full max-w-95 translate-0 sm:h-max sm:rounded-bl-3xl lg:hidden"
                >
                    <NavMenu />
                </DialogContent>
            </Dialog>
            <AccountDropdown
                className="hidden lg:block"
                email="admin@gmail.com"
            />
        </>
    );
};

export default AccountMenuControls;
