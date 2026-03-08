import type { AxiosProgressEvent } from 'axios';
import React, { useEffect, useId, useState } from 'react';
import LoadingRing from './LoadingRing';
import UploadFileBtn from './UploadFileBtn';

type FileInputProps = {
    src?: string;
    name?: string;
    filename?: string;
    isEdited: boolean;
    onChange: (file: File | null) => void;
    error?: string;
    progress?: AxiosProgressEvent | null;
};

export default function FileInput({
    src,
    filename,
    name,
    isEdited,
    onChange,
    error,
    progress,
}: FileInputProps) {
    const [currentFilename, setCurrentFilename] = useState(filename);
    const [isUpdated, setIsUpdated] = useState(false);
    const id = useId();

    useEffect(() => {
        const resetFile = () => {
            setCurrentFilename(filename);
            setIsUpdated(false);
        };

        document.addEventListener('media:clear', resetFile);
        return () => document.removeEventListener('media:clear', resetFile);
    }, [filename]);

    const handleFile = (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0] ?? null;
        if (!file) return;

        setCurrentFilename(file.name);
        setIsUpdated(true);
        onChange(file);
    };

    return (
        <div>
            <div className="flex max-w-150 flex-col items-center justify-start gap-10 md:flex-row">
                {isEdited && (
                    <input
                        type="file"
                        onChange={handleFile}
                        className="mt-1 hidden"
                        id={id}
                        name={name ?? `file-input-${id}`}
                    />
                )}

                <div className="shrink-0">
                    <UploadFileBtn
                        id={id}
                        disabled={!isEdited}
                        label="Загрузить файл"
                    />
                </div>

                <div>
                    {currentFilename ? (
                        <a
                            href={isUpdated ? undefined : src}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="flex items-center gap-2 text-sm text-primary underline underline-offset-2"
                        >
                            📄 {currentFilename}
                        </a>
                    ) : (
                        <span className="text-sm text-zinc-400">
                            Файл не выбран
                        </span>
                    )}
                    {error && (
                        <span className="block max-w-50 text-sm font-medium text-red-500">
                            {error}
                        </span>
                    )}
                </div>

                <LoadingRing
                    progress={progress}
                    updated={isUpdated}
                />
            </div>
        </div>
    );
}
