<script setup lang="ts">
import { SidebarProvider } from '@/components/ui/sidebar';
import { onMounted, ref } from 'vue';
import { useToastsStore } from '@/stores/ToastsStore';
import { X } from 'lucide-vue-next';

interface Props {
    variant?: 'header' | 'sidebar';
}

defineProps<Props>();

const isOpen = ref(true);
const toasts = useToastsStore();

onMounted(() => {
    isOpen.value = localStorage.getItem('sidebar') !== 'false';
});

const handleSidebarChange = (open: boolean) => {
    isOpen.value = open;
    localStorage.setItem('sidebar', String(open));
};
</script>

<template>
    <div v-if="variant === 'header'" class="flex min-h-screen w-full flex-col">
        <slot />
    </div>
    <SidebarProvider v-else :default-open="isOpen" :open="isOpen" @update:open="handleSidebarChange">
        <slot />
    </SidebarProvider>
    <ul class="fixed bottom-0 right-0 p-3 mr-3 mb-5">
        <li class="w-full md:min-w-96 block p-4 rounded bg-green-600" v-for="toast in toasts.toasts" :key="toast.id">
            <button type="button" class="absolute right-0 top-0 mr-5 mt-6 " @click="toasts.removeToast(toast)"><X :size="16" /></button>
            <h4 class="font-bold">{{ toast.title}}</h4>
            <p class="text-sm">{{toast.description}}</p>
        </li>
    </ul>
</template>
