import { NodeProps } from '@/types/shared';
import { FC } from 'react';

const Filler: FC<NodeProps> = ({ className }) => {
    return <div className={className} />;
};

export default Filler;
