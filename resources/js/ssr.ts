import { createInertiaApp } from '@inertiajs/vue3'
import createServer from '@inertiajs/vue3/server'
import { renderToString } from '@vue/server-renderer'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { createSSRApp, DefineComponent, h } from 'vue'
import { route as ziggyRoute } from 'ziggy-js'

const appName = import.meta.env.VITE_APP_NAME || 'Laravel'

createServer((page) =>
    createInertiaApp({
        page,
        render: renderToString,
        title: (title) => `${title} - ${appName}`,
        resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob<DefineComponent>('./pages/**/*.vue')),
        setup({ App, props, plugin }) {
            const app = createSSRApp({ render: () => h(App, props) })

            // Configure Ziggy for SSR...
            const ziggyConfig = {
                ...(page.props.ziggy || {}),
                // @ts-expect-error location might not be defined
                location: new URL(page.props.ziggy?.location),
            }

            // Create a route function...
            // @ts-expect-error mismatch on return type
            const route = (name: string, params?: any, absolute?: boolean) => ziggyRoute(name, params, absolute, ziggyConfig)

            // Make the route function available globally...
            // @ts-expect-error type mismatch
            app.config.globalProperties.route = route

            // Make the route function available globally for SSR...
            if (typeof window === 'undefined') {
                // @ts-expect-error global is undefined on frontend
                global.route = route
            }

            app.use(plugin)

            return app
        },
    }),
)
