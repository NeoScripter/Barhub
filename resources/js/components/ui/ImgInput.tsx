import Placeholder from '@/assets/images/shared/placeholder.webp';
import type { AxiosProgressEvent } from 'axios';
import React, { useEffect, useId, useState } from 'react';
import LoadingRing from './LoadingRing';
import UploadFileBtn from './UploadFileBtn';
import DialogLayout from '../layout/DialogLayout';

type ImgInputProps = {
    src?: string;
    isEdited: boolean;
    onChange: (file: File | null) => void;
    error?: string;
    progress?: AxiosProgressEvent | null;
    altText: string;
    altError?: string;
    onAltChange: (value: string) => void;
    label?: string;
};

export default function ImgInput({
    src,
    isEdited,
    onChange,
    error,
    progress,
    altText,
    altError,
    onAltChange,
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

    const [showPopup, setShowPopup] = useState(false);

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

                    {isEdited && (
                        <AltInput
                            altText={altText}
                            altError={altError}
                            onAltChange={onAltChange}
                        />
                    )}
                </div>
                <div>
                    <div
                        onClick={() => setShowPopup(true)}
                        className="transition-scale relative flex size-40 cursor-pointer items-center justify-center duration-200 ease-in hover:scale-110"
                    >
                        <img
                            src={preview ?? Placeholder}
                            alt="Preview"
                            className="h-full w-full rounded object-cover"
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

            <DialogLayout
                show={showPopup}
                onClose={() => setShowPopup(false)}
                className="mx-auto w-fit max-w-260"
            >
                {' '}
                <div className="mx-auto w-fit">
                    <img
                        src={preview}
                        alt={altText}
                    />
                </div>
            </DialogLayout>
        </div>
    );
}

type AltInputProps = {
    altText: string;
    altError?: string;
    onAltChange: (value: string) => void;
};

function AltInput({ altText, altError, onAltChange }: AltInputProps) {
    const id = useId();

    return (
        <div className="grid gap-1 text-xs">
            <label
                htmlFor={id}
                className="mt-3"
            >
                Альтернативный текст для фото
            </label>
            <textarea
                name="Альтернативный текст"
                id={id}
                value={altText}
                onChange={(e) => onAltChange(e.target.value)}
                className="focus-visible:border-ring focus-visible:ring-dark-swamp/80 min-h-15 rounded-sm border border-zinc-700 p-1 shadow-xs transition-[color,box-shadow] outline-none focus-visible:ring-[2px]"
            />
            <span className="font-medium text-red-600">{altError}</span>
        </div>
    );
}
