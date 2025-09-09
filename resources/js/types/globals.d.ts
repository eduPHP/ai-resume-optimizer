import type { route as routeFn } from 'ziggy-js'
import Echo, { type BroadcastDriver } from 'laravel-echo'

declare global {
    const route: typeof routeFn
    interface Window {
        Echo: Echo<BroadcastDriver>
        Laravel: { api_token: string }
    }
}
