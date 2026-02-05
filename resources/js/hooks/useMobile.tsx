import { useSyncExternalStore } from 'react';

const MOBILE_BREAKPOINT = 768;
const TABLET_BREAKPOINT = 1024;
const DESKTOP_BREAKPOINT = 1280;

const createMediaQuery = (maxWidth: number) =>
    typeof window === 'undefined'
        ? undefined
        : window.matchMedia(`(max-width: ${maxWidth - 1}px)`);

const mobileMql = createMediaQuery(MOBILE_BREAKPOINT);
const tabletMql = createMediaQuery(TABLET_BREAKPOINT);
const desktopMql = createMediaQuery(DESKTOP_BREAKPOINT);

function createMediaQueryListener(mql: MediaQueryList | undefined) {
    return (callback: (event: MediaQueryListEvent) => void) => {
        if (!mql) {
            return () => {};
        }
        mql.addEventListener('change', callback);
        return () => {
            mql.removeEventListener('change', callback);
        };
    };
}

function createBreakpointChecker(mql: MediaQueryList | undefined) {
    return (): boolean => {
        return mql?.matches ?? false;
    };
}

function getServerSnapshot(): boolean {
    return false;
}

// Mobile: < 768px
export function useIsMobile(): boolean {
    return useSyncExternalStore(
        createMediaQueryListener(mobileMql),
        createBreakpointChecker(mobileMql),
        getServerSnapshot,
    );
}

// Tablet: < 1024px
export function useIsTablet(): boolean {
    return useSyncExternalStore(
        createMediaQueryListener(tabletMql),
        createBreakpointChecker(tabletMql),
        getServerSnapshot,
    );
}

// Desktop: < 1280px (anything above is considered large desktop)
export function useIsDesktop(): boolean {
    return useSyncExternalStore(
        createMediaQueryListener(desktopMql),
        createBreakpointChecker(desktopMql),
        getServerSnapshot,
    );
}
