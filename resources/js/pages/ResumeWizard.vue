<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { Steps } from '../components/ui/steps';
import { useResumeWizardStore, Optimization } from '@/stores/ResumeWizardStore';
import { onMounted, ref } from 'vue';
import { File } from 'lucide-vue-next';
import { downloadPDF } from '@/lib/axios';
import { Button } from '@/components/ui/button';

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
const compatibilityPercentageStyle = ref<string>('')
const page = usePage();

const breadcrumbs = ref<BreadcrumbItem[]>([
    {
        title: 'New Optimization',
        href: '/optimizations/create',
    },
]);

onMounted(() => {
    state.setOptimization(props.optimization as Optimization)

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
    if (props.optimization && state.form.status === 'complete') {
        const score = state.form.response.compatibility_score;
        switch (true) {
            case score < 90 && score > 84:
                compatibilityPercentageStyle.value = 'text-yellow-400'
                break;
            case score < 85:
                compatibilityPercentageStyle.value = 'text-red-400'
                break;
            default:
                compatibilityPercentageStyle.value = 'text-green-400 text-xl'
        }
    }
})

</script>

<template>
    <Head title="New Optimization" />

    <AppLayout :breadcrumbs="breadcrumbs">

        <div v-if="state.form.status !== 'complete'" class="mx-auto flex h-full w-[950px] flex-1 flex-col gap-4 rounded-xl p-4">
            <Steps />
            <component :is="{...state.currentStep.stepComponent}" />
        </div>
        <div v-if="state.form.status === 'complete' && state.form.response" class="mx-auto flex h-full w-[950px] flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="bg-gray-300/10 dark:bg-[#202020] px-8 py-6">
                <h1 class="text-2xl">{{ state.form.role.company }} {{ state.form.role.name }} Application</h1>

                <h2 class="mt-6 font-bold">Role Specific Professional Summary</h2>
                <p class="text-gray-400">{{ state.form.response.professional_summary }}</p>

                <hr class="my-8 max-w-xl border-t border-gray-300">

                <h4 class="mt-4">Compatibility score: <span class="font-bold" :class="compatibilityPercentageStyle">{{state.form.response.compatibility_score}}%</span> Match</h4>
                <h2 class="mt-4 font-bold">Strong Alignments:</h2>
                <div v-for="alignment in state.form.response.strong_alignments" :key="alignment.title" class="mt-4">
                    <h3>{{alignment.title}}</h3>
                    <p class="text-gray-400">{{ alignment.description }}</p>
                </div>

                <h2 v-if="state.form.response.moderate_gaps.length" class="mt-6 text-yellow-400 font-bold">Moderate Gaps:</h2>

                <div v-if="state.form.response.moderate_gaps.length" class="text-yellow-400">
                    <ul>
                        <li v-for="gap in state.form.response.moderate_gaps" :key="gap.title" >
                            <span class="block">{{ gap.title }}</span>
                            <span class="block text-yellow-400/70">{{ gap.description }}</span>
                        </li>
                    </ul>
                </div>

                <h2 v-if="state.form.response.missing_requirements.length" class="mt-6 text-red-400 font-bold">Missing Requirements:</h2>

                <div v-if="state.form.response.missing_requirements.length" class="text-red-400">
                    <ul>
                        <li v-for="missing in state.form.response.missing_requirements" :key="missing.title" >
                            <span class="block">{{ missing.title }}</span>
                            <span class="block text-red-400/70">{{ missing.description }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="flex justify-end">
                <Button type="button" @click="downloadPDF(page, state)">
                    <File class="!w-4 !h-4" />
                    Download Optimized Resume
                </Button>
            </div>
        </div>
    </AppLayout>
</template>
