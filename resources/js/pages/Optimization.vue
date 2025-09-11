<script setup lang="ts">
import CompleteOptimization from '@/components/CompleteOptimization.vue'
import OptimizationWizard from '@/components/OptimizationWizard.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { OptimizationType, useOptimizationWizardStore } from '@/stores/OptimizationWizardStore'
import type { BreadcrumbItem } from '@/types'
import { Head } from '@inertiajs/vue3'
import { onMounted, ref } from 'vue'
import InfiniteProgressBar from '@/components/ui/InfiniteProgressBar.vue'
import { TriangleAlert } from 'lucide-vue-next'
import { differenceInMinutes } from 'date-fns'
import RetryBlock from '@/components/RetryBlock.vue'

const state = useOptimizationWizardStore()

const props = defineProps({
    step: {
        type: Number,
        default: 0,
    },
    optimization: {
        type: Object,
        default: () => ({}),
    },
})

const breadcrumbs = ref<BreadcrumbItem[]>([
    {
        title: 'New Optimization',
        href: '/optimizations/create',
    },
])


onMounted(() => {
    state.setOptimization(props.optimization as OptimizationType)

    if (state.form.optimizationId) {
        breadcrumbs.value = [
            {
                title: 'Optimizations',
                href: '#',
            },

            {
                title: state.form.role.company,
                href: '/optimizations/' + state.form.optimizationId,
            },
        ]
    }
})

</script>

<template>
    <Head :title="state.pageTitle" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div v-if="state.form.status === 'processing'" class="flex h-full flex-col items-center justify-center space-y-2">
            <h1 class="text-2xl font-bold">Optimization In Progress</h1>
            <InfiniteProgressBar class="max-w-xs" />
            <p class="text-center text-gray-400">
                This might take a minute or two or three... <br />
                You will be notified (here) when it is done ;)
            </p>
            <RetryBlock v-if="differenceInMinutes(new Date(), state.form.updated) > 6" />
        </div>
        <div v-if="state.form.status === 'failed'" class="flex h-[85vh] flex-col items-center justify-center px-4">
            <h1 class="flex items-center text-2xl font-bold">
                <TriangleAlert class="mb-1 mr-2 text-yellow-600 dark:text-yellow-300" />Optimization Failed
            </h1>
            <p class="mt-2 text-center text-gray-400">
                There were some problem processing this optimization request. <br />
                Although we won't provide you with an specific reason yet, you can retry it if you want.
            </p>
            <RetryBlock />
        </div>
        <OptimizationWizard v-else-if="['draft', 'pending', 'editing'].includes(state.form.status)" />
        <CompleteOptimization v-else-if="state.form.status === 'complete'" />
    </AppLayout>
</template>
