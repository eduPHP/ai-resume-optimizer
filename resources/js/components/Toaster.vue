<script lang="ts" setup>
import { useToastsStore } from '@/stores/ToastsStore';
import { X } from 'lucide-vue-next';

const toasts = useToastsStore();

</script>

<template>
    <!-- ... other template code ... -->
    <ul class="fixed bottom-0 right-0 p-3 mr-3 mb-5">
        <TransitionGroup
            name="slide-fade"
            tag="div"
            class="space-y-4"
        >
            <li
                v-for="toast in toasts.toasts"
                :key="toast.id"

            >
                <div class="w-full md:min-w-96 block p-4 rounded"
                     :class="{
                    'bg-green-600': toast.type === 'success',
                    'bg-red-600': toast.type === 'error',
                }">
                    <button type="button" class="absolute right-0 top-0 mr-5 mt-6" @click="toasts.removeToast(toast)">
                        <X :size="16" />
                    </button>
                    <h4 class="font-bold">{{ toast.title }}</h4>
                    <p class="text-sm">
                        {{ toast.description }}
                    </p>
                </div>
                <div
                    class="-mt-1 h-1 bg-white/30 transition-all duration-linear"
                    :style="{ width: `${toast.progress.toFixed()}%` }"
                />
            </li>
        </TransitionGroup>
    </ul>
</template>

<style scoped>
.slide-fade-enter-active {
    transition: all 0.3s ease-out;
}

.slide-fade-leave-active {
    transition: all 0.3s cubic-bezier(1, 0.5, 0.8, 1);
}

.slide-fade-enter-from {
    transform: translateY(20px);
    opacity: 0;
}

.slide-fade-leave-to {
    transform: translateY(0);
    opacity: 0;
}
</style>
