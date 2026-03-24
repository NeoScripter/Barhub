import { Button } from '@/components/ui/Button';
import { DeleteAlertDialog } from '@/components/ui/DeleteAlertDialog';
import { cn } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import { destroy } from '@/wayfinder/routes/admin/admins';
import { App, Inertia } from '@/wayfinder/types';
import { router, usePage } from '@inertiajs/react';
import { Trash2 } from 'lucide-react';
import { FC, useState } from 'react';
import { toast } from 'sonner';

const AdminRow: FC<
    NodeProps<{
        user: Pick<App.Models.User, 'id' | 'name' | 'email' | 'last_login_at'>;
    }>
> = ({ className, user }) => {
    const { exhibition } =
        usePage<Inertia.Pages.Admin.Admins.Index>().props;
    const [isDeleting, setIsDeleting] = useState(false);

    const handleDelete = () => {
        setIsDeleting(true);

        router.delete(destroy({ exhibition, admin: user.id }).url, {
            onSuccess: () => {
                toast.success(
                    'Пользователь больше не является администратором данной выставки',
                );
            },
            onError: () => {
                toast.error('Ошибка удаления администратора');
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
                title="Удалить администратора?"
                description={`Вы уверены, что хотите удалить данного администратора?`}
                onConfirm={handleDelete}
                confirmText="Удалить"
                cancelText="Отмена"
                isLoading={isDeleting}
            />
        </li>
    );
};

export default AdminRow;

const Attribute: FC<{ label: string; content: string }> = ({
    label,
    content,
}) => (
    <div>
        <span className="mb-2 block font-bold text-primary">{label}</span>
        <span>{content}</span>
    </div>
);
