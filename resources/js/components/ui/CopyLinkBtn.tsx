import { Button } from '@/components/ui/Button';
import { NodeProps } from '@/types/shared';
import { Link } from 'lucide-react';
import { FC } from 'react';
import { toast } from 'sonner';

const CopyLinkBtn: FC<NodeProps<{ url: string }>> = ({ className, url }) => {
    const handleCopyLink = () => {
        navigator.clipboard.writeText(url);
        toast.success('Ссылка скопирована');
    };

    return (
        <Button
            variant="muted"
            onClick={handleCopyLink}
            type="button"
            className={className}
        >
            Копировать ссылку
            <Link />
        </Button>
    );
};

export default CopyLinkBtn;
