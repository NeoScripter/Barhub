import { SelectMenu } from '@/components/ui/SelectMenu';
import { X } from 'lucide-react';
import { useState } from 'react';

type Theme = {
    id: number;
    name: string;
    color_hex: string;
};

type ThemeSelectProps = {
    availableThemes: Theme[];
    selectedThemeIds: number[];
    onChange: (themeIds: number[]) => void;
};

export function ThemeSelect({
    availableThemes,
    selectedThemeIds,
    onChange,
}: ThemeSelectProps) {
    const [selectValue, setSelectValue] = useState<string>('');

    // Get themes that are already selected
    const selectedThemes = availableThemes.filter((theme) =>
        selectedThemeIds.includes(theme.id),
    );

    // Get themes that can still be selected
    const availableToSelect = availableThemes.filter(
        (theme) => !selectedThemeIds.includes(theme.id),
    );

    const addTheme = (themeId: string) => {
        if (!themeId) return;

        const id = parseInt(themeId);
        if (!selectedThemeIds.includes(id)) {
            onChange([...selectedThemeIds, id]);
        }

        // Reset select
        setSelectValue('');
    };

    const removeTheme = (themeId: number) => {
        onChange(selectedThemeIds.filter((id) => id !== themeId));
    };

    return (
        <div className="grid gap-3">
            <SelectMenu
                items={availableToSelect}
                value={selectValue}
                onValueChange={addTheme}
                getLabel={(theme) => theme.name}
                getValue={(theme) => theme.id.toString()}
                placeholder="Добавить направление"
                className="rounded-md"
                disabled={availableToSelect.length === 0}
            />

            {selectedThemes.length > 0 && (
                <div className="flex flex-wrap gap-2">
                    {selectedThemes.map((theme) => (
                        <div
                            key={theme.id}
                            className="flex text-foreground items-center gap-2 rounded-md px-3 py-1.5 text-sm"
                            style={{ backgroundColor: theme.color_hex }}
                        >
                            <span>{theme.name}</span>
                            <button
                                type="button"
                                onClick={() => removeTheme(theme.id)}
                                className="hover:opacity-70"
                            >
                                <X className="h-4 w-4" />
                            </button>
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
}
