import { RouteParams, Router } from 'ziggy-js';

declare global {
    const route: (name: string, params?: any, absolute?: boolean) => string;
    function route(): Router;
    function route(name: string, params?: RouteParams<typeof name> | undefined, absolute?: boolean): string;
}

declare module '@vue/runtime-core' {
    interface ComponentCustomProperties {
        route: typeof route;
    }
}
