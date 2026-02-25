import { cn } from '@/lib/utils';
import { NodeProps } from '@/types/shared';
import { Check } from 'lucide-react';
import { FC } from 'react';

type ColorPickerProps = NodeProps<{
    colors: string[];
    selectedColor: string;
    onColorChange: (color: string) => void;
}>;

const ColorPicker: FC<ColorPickerProps> = ({
    className,
    colors,
    selectedColor,
    onColorChange,
}) => {
    return (
        <div className={cn('flex flex-wrap gap-3', className)}>
            {colors.map((color) => (
                <button
                    key={color}
                    type="button"
                    onClick={() => onColorChange(color)}
                    className={cn(
                        'relative size-9 rounded-full border-2 transition-all hover:scale-110',
                        selectedColor === color
                            ? 'border-foreground/50 ring-2 ring-blue-300/75 ring-offset-2'
                            : 'border-transparent hover:border-muted-foreground',
                    )}
                    style={{ backgroundColor: color }}
                    aria-label={`Select color ${color}`}
                >
                    {selectedColor === color && (
                        <Check className="absolute inset-0 m-auto h-5 w-5 text-white drop-shadow-lg" />
                    )}
                </button>
            ))}
        </div>
    );
};

export default ColorPicker;
