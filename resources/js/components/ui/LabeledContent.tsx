import { NodeProps } from '@/types/shared';
import { FC } from 'react';

const LabeledContent: FC<NodeProps<{ label: string }>> = ({
    className,
    children,
    label,
}) => {
    return (
        <div className='max-w-200 text-pretty'>
            <h3 className="mb-2 text-lg font-bold">{label}</h3>
            <div className={className}>{children}</div>
        </div>
    );
};

export default LabeledContent;
