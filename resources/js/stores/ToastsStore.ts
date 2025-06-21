import { defineStore } from 'pinia'

export type Toast = {
    title: string;
    description: string;
    type: 'success' | 'error';
    id?: string;
    duration?: number;
}
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
                duration,
            })
        },

        error(title: string, description: string, duration?: number) {
            this.addToast({
                title,
                description,
                type: 'error',
                duration,
            })
        },

        addToast(toast: Toast) {
            if (! toast.id) toast.id = Math.random().toString()

            this.toasts.push(toast)

            setTimeout(() => {
                this.toasts = this.toasts.filter(t => t.id !== toast.id)
            }, toast.duration ?? 5000)
        },

        removeToast(toast: Toast) {
            this.toasts = this.toasts.filter(t => t.id !== toast.id)
        }
    },
})
