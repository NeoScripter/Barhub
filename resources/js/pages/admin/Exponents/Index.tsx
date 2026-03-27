import { DeleteAlertDialog } from '@/components/ui/DeleteAlertDialog';
import CompanyLayout from '@/layouts/app/CompanyLayout';
import { destroy } from '@/wayfinder/routes/admin/emails';
import { Inertia } from '@/wayfinder/types';
import { router } from '@inertiajs/react';
import { FC, useState } from 'react';
import { toast } from 'sonner';
import CreateExponent from './partials/CreateExponent';
import ExponentDialog from './partials/ExponentDialog';
import ExponentRow from './partials/ExponentRow';
import SelectExponent from './partials/SelectExponent';

const Index: FC<Inertia.Pages.Admin.Exponents.Index> = ({
    exponents,
    company,
    pending,
}) => {
    const [isLoading, setIsLoading] = useState(false);

    const handleDeleteClick = (id: number) => {
        router.delete(destroy({ company, email: id }).url, {
            onSuccess: () => toast.success('Доступ успешно удален!'),
            onStart: () => setIsLoading(true),
            onFinish: () => setIsLoading(false),
            preserveScroll: true
        });
    };

    return (
        <CompanyLayout className="space-y-30!">
            {Array.isArray(pending) && pending.length > 0 && (
                <div>
                    <h3 className="mb-4 font-bold">Ожидают регистрации</h3>
                    <ul className="space-y-2">
                        {pending.map((entry) => (
                            <li
                                key={entry.id}
                                className="flex items-center gap-2"
                            >
                                {' '}
                                <span>{entry.email}</span>
                                <DeleteAlertDialog
                                    trigger={
                                        <button className="flex size-5 items-center justify-center p-3 text-xl text-red-600">
                                            &times;
                                        </button>
                                    }
                                    title="Удалить доступ экпонента?"
                                    description={`Вы уверены, что хотите удалить доступ для данного экпонента? Это действие нельзя отменить.`}
                                    onConfirm={() =>
                                        handleDeleteClick(entry.id)
                                    }
                                    confirmText="Удалить"
                                    cancelText="Отмена"
                                    isLoading={isLoading}
                                />
                            </li>
                        ))}
                    </ul>
                </div>
            )}
            <ul
                id="exponent-list"
                className="mb-8 space-y-14 sm:mb-14 lg:mb-16"
            >
                {exponents.map((exponent) => (
                    <ExponentRow
                        key={exponent.id}
                        user={exponent}
                    />
                ))}
            </ul>

            <div className="flex flex-wrap items-center justify-center gap-4">
                <ExponentDialog
                    key="select-exponent"
                    label="Выбрать из списка"
                >
                    <SelectExponent />
                </ExponentDialog>

                <ExponentDialog
                    key="create-exponent"
                    label="Создать"
                >
                    <CreateExponent />
                </ExponentDialog>


            </div>
        </CompanyLayout>
    );
};

export default Index;
