import { cn } from '@/lib/utils';
import { FC } from 'react';

const BurgerMenu: FC<{
    className?: string;
    onClick: () => void;
    show: boolean;
}> = ({ className, onClick, show }) => {
    return (
        <button
            onClick={onClick}
            id="burger-menu"
            className={cn(
                'size-9 cursor-pointer transition-all isolate duration-300 ease-in relative',
                {
                    'text-gray-400': show,
                    'text-primary': !show,
                },
                className,
            )}
        >
            <span className="hidden pointer-coarse:absolute z-5 pointer-coarse:-inset-4 pointer-coarse:block!" />
            <svg
                xmlns="http://www.w3.org/2000/svg"
                width="100%"
                height="100%"
                viewBox="0 0 36 36"
                fill="none"
                stroke="currentColor"
                strokeWidth="3"
                strokeLinecap="round"
                strokeLinejoin="round"
                className={cn(
                    'lucide lucide-menu-icon lucide-menu overflow-visible',
                )}
            >
                <path
                    className={cn(
                        'burger',
                        show
                            ? 'burger-open rotate-45'
                            : 'burger-close -translate-y-[9px]',
                    )}
                    d="M0 18h36"
                />
                <path
                    className={cn(
                        'transition-opacity duration-300 ease-in',
                        show ? 'opacity-0 delay-0' : 'delay-150',
                    )}
                    d="M0 18h36"
                />
                <path
                    className={cn(
                        'burger',
                        show
                            ? 'burger-open -rotate-45'
                            : 'burger-close translate-y-[9px]',
                    )}
                    d="M0 18h36"
                />
            </svg>
        </button>
    );
};

export default BurgerMenu;
