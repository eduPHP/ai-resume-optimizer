import { defineStore } from 'pinia'

export type Toast = {
    title: string;
    description: string;
    type: 'success' | 'error';
    id?: string;
    duration?: number;
    state: 'active' | 'closed';
    timeout?: number;
    progress: number;
    progressInterval?: number;
};

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
                progress: 100,
            })
        },

        error(title: string, description: string, duration?: number) {
            this.addToast({
                title,
                description,
                type: 'error',
                duration: duration ?? 5000,
                state: 'active',
                progress: 100,
            })
        },

        addToast(toast: Toast) {
            if (!toast.id) toast.id = Math.random().toString()

            this.toasts.push(toast)

            // Update progress every 10ms
            const duration = toast.duration ?? 5000
            const step = 100 / (duration / 10)

            toast.progressInterval = setInterval(() => {
                const index = this.toasts.findIndex(t => t.id === toast.id)
                if (index !== -1) {
                    this.toasts[index].progress = Math.max(0, this.toasts[index].progress - step)
                }
            }, 10)

            toast.timeout = setTimeout(() => {
                this.removeToast(toast)
            }, duration)
        },

        removeToast(toast: Toast) {
            const toastItemIndex = this.toasts.findIndex(t => t.id === toast.id)
            if (toastItemIndex === -1) return

            clearTimeout(this.toasts[toastItemIndex].timeout)
            clearInterval(this.toasts[toastItemIndex].progressInterval)
            this.toasts[toastItemIndex].state = 'closed'

            setTimeout(() => {
                this.toasts = this.toasts.filter(t => t.id !== toast.id)
            }, 400)
        }
    },
})
