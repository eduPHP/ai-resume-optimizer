<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Steps } from '../components/ui/steps';
import { useResumeWizardStore, Optimization } from '@/stores/ResumeWizardStore';
import { onMounted } from 'vue'

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
const state = useResumeWizardStore()

onMounted(() => {
    if (props.optimization) {
        state.setOptimization(props.optimization as Optimization)
    }
})

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'New Optimization',
        href: '/optimizations/create',
    },
];


</script>

<template>
    <Head title="New Optimization" />

    <AppLayout :breadcrumbs="breadcrumbs">

        <div class="mx-auto flex h-full w-[950px] flex-1 flex-col gap-4 rounded-xl p-4">
            <Steps />
            <component :is="{...state.currentStep.stepComponent}" />
        </div>
    </AppLayout>
</template>
