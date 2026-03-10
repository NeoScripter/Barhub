import { cn } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import { FC } from 'react';

type Props = NodeProps<
    {
        filename: string;
    } & React.AnchorHTMLAttributes<HTMLAnchorElement>
>;

const DownloadFileLink: FC<Props> = ({
    className,
    filename,
    ...props
}) => {
    return (
        <a
            target="_blank"
            rel="noopener noreferrer"
            {...props}
            className={cn(
                'flex items-center gap-2 text-sm text-primary underline underline-offset-2',
                className,
            )}
        >
            📄 {filename}
        </a>
    );
};

export default DownloadFileLink;
