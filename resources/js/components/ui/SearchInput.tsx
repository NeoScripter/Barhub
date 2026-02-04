import { cn } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import { Search } from 'lucide-react';
import { ChangeEvent, FC } from 'react';
import { Input } from './Input';

const SearchInput: FC<
    NodeProps<{ value: string; handleChange: (val: string) => void }>
> = ({ className, value, handleChange }) => {
    return (
        <div className={cn('relative', className)}>
            <Input
                className="border-muted pr-10"
                type="search"
                value={value}
                placeholder="Enter project name"
                onChange={(e: ChangeEvent<HTMLInputElement>) =>
                    handleChange(e.currentTarget.value)
                }
            />
            <Search className="absolute top-0 right-3 block size-5 translate-y-[40%] text-muted" />
        </div>
    );
};

export default SearchInput;
