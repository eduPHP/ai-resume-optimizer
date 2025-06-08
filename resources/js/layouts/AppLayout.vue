<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import { Loader } from 'lucide-vue-next';
import { useOptimizationWizardStore } from '@/stores/OptimizationWizardStore';

const state = useOptimizationWizardStore();

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div v-if="state.optimizing" class="fixed flex flex-col items-center justify-center gap-2 z-20 inset-0 bg-black/70 text-white">
            <h1 class="text-4xl">Optimizing <Loader class="animate-spin inline-block" /></h1>
            <p class="text-gray-400">This might take a minute or two or three...</p>
        </div>
        <slot />
    </AppLayout>
</template>
