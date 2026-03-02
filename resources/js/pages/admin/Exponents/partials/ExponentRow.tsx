import { Button } from '@/components/ui/Button';
import { cn } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import { destroy } from '@/wayfinder/routes/admin/exhibitions/exponents';
import { App, Inertia } from '@/wayfinder/types';
import { Link, usePage } from '@inertiajs/react';
import { Trash2 } from 'lucide-react';
import { FC } from 'react';
import { toast } from 'sonner';

const ExponentRow: FC<
    NodeProps<{
        user: Pick<App.Models.User, 'id' | 'name' | 'email' | 'last_login_at'>;
    }>
> = ({ className, user }) => {
    const { exhibition, company } =
        usePage<Inertia.Pages.Admin.Exponents.Index>().props;

    return (
        <li
            className={cn(
                'grid grid-cols-[repeat(auto-fit,minmax(12.5rem,1fr))] gap-4',
                className,
            )}
        >
            <Attribute
                label="Имя"
                key="name"
                content={user.name}
            />
            <Attribute
                label="Логин (e-mail)"
                key="email"
                content={user.email}
            />
            <Attribute
                label="Последний вход"
                key="lastLogin"
                content={user.last_login_at ?? 'Отсутствует'}
            />
            <Button
                variant="destructive"
                className="w-fit"
                asChild
            >
                <Link
                    href={
                        destroy({ exhibition, company, exponent: user.id }).url
                    }
                    method='delete'
                    onSuccess={() =>
                        toast.success(
                            'Пользователь удален из экспонентов данной компании',
                        )
                    }
                >
                    <Trash2 />
                    Удалить
                </Link>
            </Button>
        </li>
    );
};

export default ExponentRow;

const Attribute: FC<{ label: string; content: string }> = ({
    label,
    content,
}) => (
    <div>
        <span className="mb-2 block font-bold text-primary">{label}</span>
        <span>{content}</span>
    </div>
);
