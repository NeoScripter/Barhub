import Placeholder from '@/assets/images/shared/placeholder.webp';
import type { AxiosProgressEvent } from 'axios';
import React, { useEffect, useId, useState } from 'react';
import LoadingRing from './LoadingRing';
import UploadFileBtn from './UploadFileBtn';

type ImgInputProps = {
    src?: string;
    isEdited: boolean;
    onChange: (file: File | null) => void;
    error?: string;
    progress?: AxiosProgressEvent | null;
    label?: string;
};

export default function ImgInput({
    src,
    isEdited,
    onChange,
    error,
    progress,
    label = 'Главное фото',
}: ImgInputProps) {
    const [preview, setPreview] = useState(src);
    const id = useId();

    useEffect(() => {
        const resetImage = () => setPreview(src);

        document.addEventListener('media:clear', resetImage);
        return () => document.removeEventListener('media:clear', resetImage);
    }, [src]);

    const handleFile = (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0] ?? null;
        if (!file) return;

        setPreview(URL.createObjectURL(file));
        onChange(file);
    };

    return (
        <div>
            <p className="mb-2 text-center font-semibold sm:text-lg md:text-left">
                {label}
            </p>
            <div className="flex max-w-150 flex-col items-center justify-start gap-10 md:flex-row">
                {isEdited && (
                    <input
                        type="file"
                        accept="image/*"
                        onChange={handleFile}
                        className="mt-1 hidden"
                        id={id}
                        name={`image-input-${id}`}
                    />
                )}

                <div className="shrink-0">
                    <UploadFileBtn
                        id={id}
                        disabled={!isEdited}
                        label="Загрузить фото"
                    />
                </div>
                <div>
                    <div className="transition-scale relative flex size-40 items-center justify-center duration-200 ease-in hover:scale-110">
                        <img
                            src={preview ?? Placeholder}
                            alt="Preview"
                            className="h-full w-full rounded object-cover"
                            data-test={preview ? 'image-present' : null}
                        />
                    </div>
                    {error && (
                        <span className="block max-w-50 text-sm font-medium text-red-500">
                            {error}
                        </span>
                    )}
                </div>

                <LoadingRing
                    progress={progress}
                    updated={preview !== src}
                />
            </div>
        </div>
    );
}
