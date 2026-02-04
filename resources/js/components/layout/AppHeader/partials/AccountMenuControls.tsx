import BurgerMenuIcon from '@/components/ui/BurgerMenuIcon';
import { Dialog, DialogContent, DialogTrigger } from '@/components/ui/Dialog';
import { usePage } from '@inertiajs/react';
import AccountDropdown from './AccountDropdown';
import NavMenu from './NavMenu';

const AccountMenuControls = () => {
    const { auth } = usePage<{
        auth: ShareData;
    }>().props;

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
            {auth?.user && (
                <AccountDropdown
                    className="hidden lg:block"
                    email={auth.user.email}
                />
            )}
        </>
    );
};

export default AccountMenuControls;
