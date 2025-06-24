<script setup lang="ts">

import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import type { BreadcrumbItem } from '@/types';
import ResumeWizard from '@/components/ResumeWizard.vue';
import CompleteOptimization from '@/components/CompleteOptimization.vue';
import { OptimizationType, useOptimizationWizardStore } from '@/stores/OptimizationWizardStore';

const state = useOptimizationWizardStore()

const props = defineProps({
    step: {
        type: Number,
        default: 0
    },
    optimization: {
        type: Object,
        default: () => ({})
    }
})

const breadcrumbs = ref<BreadcrumbItem[]>([
    {
        title: 'New Optimization',
        href: '/optimizations/create',
    },
]);

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
                href: '/optimizations/'+state.form.optimizationId,
            },
        ]
    }
})

</script>

<template>
    <Head :title="state.pageTitle" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <ResumeWizard v-if="state.form.status !== 'complete'" />
        <CompleteOptimization v-if="state.form.status === 'complete'" />
    </AppLayout>
</template>
