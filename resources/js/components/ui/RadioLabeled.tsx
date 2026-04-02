import { cn } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import { Field, Label, Radio, RadioGroup } from '@headlessui/react';
import { FC } from 'react';

type Props = NodeProps & {
    value: boolean;
    rejectLabel?: string;
    label?: string;
    onChange: (value: boolean) => void;
};

const RadioLabeled: FC<Props> = ({
    className,
    value,
    onChange,
    label,
    rejectLabel = 'Отклонить',
}) => {
    return (
        <RadioGroup
            value={value}
            onChange={onChange}
            className={cn('flex flex-col items-start gap-6', className)}
        >
            {label && <h3 className="mb-2 text-lg font-bold">{label}</h3>}

            {[true, false].map((option) => (
                <Field
                    key={String(option)}
                    className="flex items-center gap-4"
                >
                    <Radio
                        value={option}
                        className="group flex size-7 items-center justify-center rounded-full border bg-white"
                        data-test={option ? 'radio-yes-btn' : 'radio-no-btn'}
                    >
                        <span className="invisible size-5 rounded-full bg-black group-data-checked:visible" />
                    </Radio>
                    <Label className="text-sm lg:text-base">
                        {option ? 'Принять' : rejectLabel}
                    </Label>
                </Field>
            ))}
        </RadioGroup>
    );
};

export default RadioLabeled;
