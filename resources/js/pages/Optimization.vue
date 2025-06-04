<script setup lang="ts">

import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import type { BreadcrumbItem } from '@/types';
import ResumeWizard from '@/pages/ResumeWizard.vue';
import CompleteOptimization from '@/components/CompleteOptimization.vue';
import { OptimizationType, useResumeWizardStore } from '@/stores/ResumeWizardStore';

const state = useResumeWizardStore()

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

    if (props.optimization) {
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
        document.title = state.form.role.company + ' - ' + state.form.role.name + ' - Resume Optimization'
    }
})

</script>

<template>
    <Head title="New Optimization" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <ResumeWizard v-if="state.form.status !== 'complete'" />
        <CompleteOptimization v-if="state.form.status === 'complete'" />
    </AppLayout>
</template>
