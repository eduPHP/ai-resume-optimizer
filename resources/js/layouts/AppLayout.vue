<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue'
import { useOptimizationWizardStore } from '@/stores/OptimizationWizardStore'
import type { BreadcrumbItemType } from '@/types'
import { Loader } from 'lucide-vue-next'

const state = useOptimizationWizardStore()

interface Props {
    breadcrumbs?: BreadcrumbItemType[]
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
})
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div v-if="state.optimizing" class="fixed inset-0 z-20 flex flex-col items-center justify-center gap-2 bg-black/70 text-white">
            <h1 class="text-4xl">Optimizing <Loader class="inline-block animate-spin" /></h1>
            <p class="text-gray-400">This might take a minute or two or three...</p>
        </div>
        <slot />
    </AppLayout>
</template>
