const DEFAULT_DELAY_MS = 500;

let debounceTimeout: ReturnType<typeof setTimeout> | null = null;

export default function debounce<T extends (...args: any[]) => any>(
    func: T,
    delayMs: number = DEFAULT_DELAY_MS
): (...args: Parameters<T>) => void {
    return (...args: Parameters<T>): void => {
        if (debounceTimeout) {
            clearTimeout(debounceTimeout);
        }
        debounceTimeout = setTimeout(() => {
            func(...args);
        }, delayMs);
    };
}
