import { defineStore } from 'pinia'

export type Toast = {
    title: string
    description: string
    type: 'success' | 'error'
    id?: string
    duration?: number
    state: 'active' | 'closed'
}

export type ToastsStore = ReturnType<typeof useToastsStore>

export const useToastsStore = defineStore('toasts', {
    state: () => ({
        toasts: [] as Toast[],
    }),

    actions: {
        success(title: string, description: string, duration?: number) {
            this.addToast({
                title,
                description,
                type: 'success',
                duration: duration ?? 5000,
                state: 'active',
            })
        },

        error(title: string, description: string, duration?: number) {
            this.addToast({
                title,
                description,
                type: 'error',
                duration: duration ?? 5000,
                state: 'active',
            })
        },

        addToast(toast: Toast) {
            if (!toast.id) toast.id = Math.random().toString()
            this.toasts.push(toast)
        },

        removeToast(toast: Toast) {
            const toastItemIndex = this.toasts.findIndex((t) => t.id === toast.id)
            if (toastItemIndex === -1) return

            this.toasts[toastItemIndex].state = 'closed'

            setTimeout(() => {
                this.toasts = this.toasts.filter((t) => t.id !== toast.id)
            }, 400)
        },
    },
})
