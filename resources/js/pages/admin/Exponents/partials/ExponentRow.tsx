import { Button } from '@/components/ui/Button';
import { cn } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import { App } from '@/wayfinder/types';
import { Trash2 } from 'lucide-react';
import { FC } from 'react';

const ExponentRow: FC<
    NodeProps<{
        user: Pick<App.Models.User, 'id' | 'name' | 'email' | 'last_login_at'>;
    }>
> = ({ className, user }) => {
    return (
        <li
            className={cn(
                'grid grid-cols-[repeat(auto-fit,minmax(12.5rem,1fr))] gap-4',
                className,
            )}
        >
            <Attribute
                label="Имя"
                key='name'
                content={user.name}
            />
            <Attribute
                label="Логин (e-mail)"
                key='email'
                content={user.email}
            />
            <Attribute
                label="Последний вход"
                key='lastLogin'
                content={user.last_login_at ?? 'Отсутствует'}
            />
            <Button
                variant="destructive"
                className="w-fit"
            >
                <Trash2 />
                Удалить
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
