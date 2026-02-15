import { router } from '@inertiajs/react';
import { useMemo } from 'react';

export const useEventFilters = (query: string) => {

        const params = new URLSearchParams(window.location.search);

    const currentFilters = useMemo(() => {
        const params = new URLSearchParams(window.location.search);

        return {
            date: params.get('filter[date]')?.split(',') || [],
            stage_id: params.get('filter[stage_id]')?.split(',') || [],
            theme: params.get('filter[theme]')?.split(',') || [],
        };
    }, [window.location.search]);

    const buildUrl = (filters: FilterConfig): string => {
        const params = new URLSearchParams();

        // Iterate over all filter keys and add them to params
        (Object.keys(filters) as Array<keyof FilterConfig>).forEach((key) => {
            const values = filters[key];
            if (values && values.length > 0) {
                params.set(`filter[${key}]`, values.join(','));
            }
        });

        const queryString = params.toString();
        return queryString ? `${baseUrl}?${queryString}` : baseUrl;
    };

    const toggleFilter = (filterKey: keyof FilterConfig, value: string) => {
        const newFilters: FilterConfig = { ...currentFilters };
        const currentValues = newFilters[filterKey] || [];

        if (currentValues.includes(value)) {
            // Remove filter value
            newFilters[filterKey] = currentValues.filter((f) => f !== value);
            if (newFilters[filterKey]?.length === 0) {
                delete newFilters[filterKey];
            }
        } else {
            // Add filter value
            newFilters[filterKey] = [...currentValues, value];
        }

        const url = buildUrl(newFilters);

        router.get(
            url,
            {},
            {
                preserveState: true,
                preserveScroll: true,
            },
        );
    };

    const clearFilters = () => {
        router.get(
            baseUrl,
            {},
            {
                preserveState: true,
                preserveScroll: true,
            },
        );
    };


    return {
        currentFilters,
        toggleFilter,
        clearFilters,
        hasActiveFilters,
    };
};
