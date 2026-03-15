import { Button } from '@/components/ui/Button';
import { DeleteAlertDialog } from '@/components/ui/DeleteAlertDialog';
import { cn } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import { destroy } from '@/wayfinder/routes/admin/exponents';
import { App, Inertia } from '@/wayfinder/types';
import { router, usePage } from '@inertiajs/react';
import { Trash2 } from 'lucide-react';
import { FC, useState } from 'react';
import { toast } from 'sonner';

const ExponentRow: FC<
    NodeProps<{
        user: Pick<App.Models.User, 'id' | 'name' | 'email' | 'last_login_at'>;
    }>
> = ({ className, user }) => {
    const { exhibition, company } =
        usePage<Inertia.Pages.Admin.Exponents.Index>().props;
    const [isDeleting, setIsDeleting] = useState(false);

    const handleDelete = () => {
        setIsDeleting(true);

        router.delete(destroy({ exhibition, company, exponent: user.id }).url, {
            onSuccess: () => {
                toast.success(
                    'Пользователь удален из экспонентов данной компании',
                );
            },
            onError: () => {
                toast.error('Ошибка удаления экспонента');
                setIsDeleting(false);
            },
        });
    };

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
            <DeleteAlertDialog
                trigger={
                    <Button
                        variant="destructive"
                        type="button"
                        className="w-fit"
                    >
                        Удалить
                        <Trash2 />
                    </Button>
                }
                title="Удалить экспонента?"
                description={`Вы уверены, что хотите удалить данного экспонента?`}
                onConfirm={handleDelete}
                confirmText="Удалить"
                cancelText="Отмена"
                isLoading={isDeleting}
            />
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
