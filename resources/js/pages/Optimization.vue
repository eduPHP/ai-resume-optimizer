<script setup lang="ts">
import CompleteOptimization from '@/components/CompleteOptimization.vue'
import OptimizationWizard from '@/components/OptimizationWizard.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { OptimizationType, useOptimizationWizardStore } from '@/stores/OptimizationWizardStore'
import type { BreadcrumbItem } from '@/types'
import { Head, router } from '@inertiajs/vue3'
import { onMounted, ref } from 'vue'
import { Button } from '@/components/ui/button'
import { createUnattendedOptimization } from '@/lib/axios'
import { useNavigationItemsStore } from '@/stores/NavigationItemsStore'
import { useToastsStore } from '@/stores/ToastsStore'

const state = useOptimizationWizardStore()
const nav = useNavigationItemsStore()
const toast = useToastsStore()

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

const retry = () => {
    state.form.status = 'processing'
    createUnattendedOptimization().then(response => {
        state.setOptimization(response.data.optimization as OptimizationType)
        nav.replace(response.data.optimization)
        toast.success('Complete', 'Optimization was successfully completed.')
        router.visit(route('optimizations.show', response.data.optimization.id), { preserveState: true })
    })
}

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
        <div v-if="state.form.status === 'processing'" class="flex flex-col items-center justify-center h-[85vh]">
            <h1 class="text-2xl font-bold">Optimization In Progress</h1>
            <p class="mt-2 text-gray-400 text-center">This might take a minute or two or three... <br> You will be notified (here) when it is done ;)</p>
        </div>
        <div v-if="state.form.status === 'failed'" class="flex flex-col items-center justify-center h-[85vh]">
            <h1 class="text-2xl font-bold">Optimization Failed</h1>
            <p class="mt-2 text-gray-400 text-center">There were some problem processing this optimization request <br> Although we won't provide you with an specific reason yet, you can retry it if you want.</p>
            <Button type="button"
                    size="lg"
                    class="flex-1 xl:flex-none select-none"
                    :class="{'cursor-not-allowed': state.loading}"
                    :disabled="state.loading"
                    @click.prevent="retry"
            >Retry</Button>
        </div>
        <OptimizationWizard v-else-if="['draft', 'pending'].includes(state.form.status)" />
        <CompleteOptimization v-else-if="state.form.status === 'complete'" />
    </AppLayout>
</template>
