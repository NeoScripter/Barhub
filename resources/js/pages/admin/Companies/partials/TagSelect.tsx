import { SelectMenu } from '@/components/ui/SelectMenu';
import { X } from 'lucide-react';
import { useState } from 'react';

type Tag = {
    id: number;
    name: string;
    color_hex: string;
};

type TagSelectProps = {
    availableTags: Tag[];
    selectedTagIds: number[];
    onChange: (tagIds: number[]) => void;
};

export function TagSelect({
    availableTags,
    selectedTagIds,
    onChange,
}: TagSelectProps) {
    const [selectValue, setSelectValue] = useState<string>('');

    // Get tags that are already selected
    const selectedTags = availableTags.filter((tag) =>
        selectedTagIds.includes(tag.id),
    );

    // Get tags that can still be selected
    const availableToSelect = availableTags.filter(
        (tag) => !selectedTagIds.includes(tag.id),
    );

    const addTag = (tagId: string) => {
        if (!tagId) return;

        const id = parseInt(tagId);
        if (!selectedTagIds.includes(id)) {
            onChange([...selectedTagIds, id]);
        }

        // Reset select
        setSelectValue('');
    };

    const removeTag = (tagId: number) => {
        onChange(selectedTagIds.filter((id) => id !== tagId));
    };

    return (
        <div className="grid gap-3">
            <SelectMenu
                items={availableToSelect}
                value={selectValue}
                onValueChange={addTag}
                getLabel={(tag) => tag.name}
                getValue={(tag) => tag.id.toString()}
                placeholder="Добавить тег"
                className="rounded-md"
                disabled={availableToSelect.length === 0}
            />

            {selectedTags.length > 0 && (
                <div className="flex flex-wrap gap-2">
                    {selectedTags.map((tag) => (
                        <div
                            key={tag.id}
                            className="flex bg-gray-400 text-white items-center gap-2 rounded-md px-3 py-1.5 text-sm"
                        >
                            <span>{tag.name}</span>
                            <button
                                type="button"
                                onClick={() => removeTag(tag.id)}
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
