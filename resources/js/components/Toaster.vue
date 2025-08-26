<script lang="ts" setup>
import { useToastsStore } from '@/stores/ToastsStore'
import Toast from '@/components/Toast.vue'

const toasts = useToastsStore()
</script>

<template>
    <!-- ... other template code ... -->
    <ul class="fixed bottom-0 right-0 mb-5 mr-3 p-3">
        <TransitionGroup name="slide-fade" tag="div" class="space-y-4">
            <Toast v-for="toast in toasts.toasts" :toast="toast" :key="toast.id" />
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
