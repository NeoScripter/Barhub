import { cn } from '@/lib/utils';
import { ImageType } from '@/types/shared';
import { useState } from 'react';

type ImageProps = {
    image: ImageType;
    wrapperStyles?: string;
    imgStyles?: string;
    isLazy?: boolean;
};

export default function Image({
    image,
    wrapperStyles,
    imgStyles,
    isLazy = true,
}: ImageProps) {
    const [isLoading, setIsLoading] = useState(true);

    const { webp, avif, tiny, alt, webp3x, webp2x, avif3x, avif2x } = image;

    return (
        <div
            className={cn('relative overflow-clip', wrapperStyles)}
            {...(alt == null && { 'aria-hidden': 'true' })}
        >
            <picture>
                {avif && (
                    <source
                        type="image/avif"
                        srcSet={`
                          ${avif} 1x,
                          ${avif2x} 2x,
                          ${avif3x} 3x
                        `}
                    />
                )}
                <source
                    type="image/webp"
                    srcSet={`
                          ${webp} 1x,
                          ${webp2x} 2x,
                          ${webp3x} 3x
                        `}
                />

                <img
                    onLoad={() => setIsLoading(false)}
                    src={webp}
                    alt={alt ?? ''}
                    loading={isLazy ? 'lazy' : undefined}
                    className={cn(
                        'size-full object-cover object-center transition-all duration-500 ease-in-out',
                        imgStyles,
                        isLoading && 'opacity-0',
                    )}
                    aria-hidden={isLoading}
                />
            </picture>

            {isLoading && (
                <div
                    role="status"
                    aria-label="Фото загружается"
                    className="absolute inset-0 z-10"
                >
                    <div
                        aria-hidden="true"
                        className="absolute inset-0 z-10 size-full animate-pulse bg-gray-200/50"
                    ></div>

                    <img
                        aria-hidden={!isLoading}
                        src={tiny}
                        alt={alt}
                        className="size-full object-cover object-center"
                    />
                </div>
            )}
        </div>
    );
}
