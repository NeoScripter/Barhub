import { useDebounce } from '@/hooks/useDebounce';
import { cn, getSearchUrl } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import { router } from '@inertiajs/react';
import { Search } from 'lucide-react';
import {
    ChangeEvent,
    FC,
    InputHTMLAttributes,
    useEffect,
    useState,
} from 'react';
import { Input } from './Input';

type Props = NodeProps<
    Omit<InputHTMLAttributes<HTMLInputElement>, 'value' | 'onChange' | 'type'>
>;

const SearchInput: FC<Props> = ({ className, ...props }) => {
    const [query, setQuery] = useState('');
    const debouncedQuery = useDebounce(query, 300);

    const handleChange = (e: ChangeEvent<HTMLInputElement>) => {
        const newQuery = e.currentTarget.value;
        setQuery(newQuery);
    };

    useEffect(() => {
        if (debouncedQuery === null) return;

        router.get(
            getSearchUrl(debouncedQuery),
            {},
            { preserveState: true, preserveScroll: true },
        );
    }, [debouncedQuery]);

    return (
        <div className={cn('relative md:w-80 lg:w-100', className)}>
            <Input
                {...props}
                type="search"
                value={query}
                onChange={handleChange}
                className={cn('border-muted pr-10', className)}
            />
            <Search className="absolute top-0 right-3 block size-5 translate-y-[40%] text-muted" />
        </div>
    );
};

export default SearchInput;
