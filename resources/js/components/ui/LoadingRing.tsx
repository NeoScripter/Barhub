import type { AxiosProgressEvent } from 'axios';
import { Check } from 'lucide-react';

type LoadingRingProps = {
    progress?: AxiosProgressEvent | null;
    updated: boolean;
};

export default function LoadingRing({ progress, updated }: LoadingRingProps) {
    const showCheckMark =
        progress && progress.percentage && progress.percentage > 99 && updated;
    const showLoadingRing =
        progress &&
        progress.percentage &&
        progress.percentage < 99 &&
        progress.percentage >= 1 &&
        updated;

    return (
        <>
            {showCheckMark ||
                (showLoadingRing && (
                    <div className="relative mx-auto flex aspect-square size-20 flex-wrap place-content-center text-xs font-bold">
                        {showLoadingRing && <Circle />}

                        {showLoadingRing && (
                            <span>{`${progress.percentage}%`}</span>
                        )}

                        {showCheckMark && (
                            <div className="animate-fade bg-bright-salad flex size-8 flex-wrap place-content-center rounded-full">
                                <Check className="text-white" />
                            </div>
                        )}
                    </div>
                ))}
        </>
    );
}

function Circle() {
    return Array(20)
        .fill(1)
        .map((_, i) => {
            const angle = (i * 360) / 20;
            return (
                <span
                    className="animate-shimmer absolute top-1/2 left-1/2 block h-3 w-[3px] -translate-1/2 bg-black"
                    style={{
                        transform: `rotate(${angle}deg) translateY(-${30}px`,
                        transformOrigin: 'center center',
                        animationDelay: `${angle * 4}ms`,
                    }}
                    key={`bar-${i}`}
                />
            );
        });
}
