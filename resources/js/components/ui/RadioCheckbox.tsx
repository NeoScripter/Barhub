import { cn } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import { Field, Label, Radio, RadioGroup } from '@headlessui/react';
import { FC } from 'react';
import Badge from './Badge';

type Props = NodeProps & {
    value: boolean;
    onChange: (value: boolean) => void;
    label?: string;
};

const RadioCheckbox: FC<Props> = ({ className, label, value, onChange }) => {
    return (
        <RadioGroup
            value={value}
            onChange={onChange}
            className={cn('', className)}
        >
            {label && <Label className="block mb-4">{label}</Label>}
            <span className="flex items-center gap-6">
                {[true, false].map((option) => (
                    <Field
                        key={String(option)}
                        className="flex items-center gap-4"
                    >
                        <Radio
                            value={option}
                            className="group flex size-7 items-center justify-center rounded-full border bg-white"
                        >
                            <span className="invisible size-5 rounded-full bg-black group-data-checked:visible" />
                        </Radio>
                        <Badge
                            variant={option ? 'success' : 'danger'}
                            className="text-xs lg:text-sm"
                        >
                            {option ? 'on' : 'off'}
                        </Badge>
                    </Field>
                ))}
            </span>
        </RadioGroup>
    );
};

export default RadioCheckbox;
