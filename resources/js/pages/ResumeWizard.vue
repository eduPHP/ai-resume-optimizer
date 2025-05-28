<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Steps } from '../components/ui/steps';
import { useResumeWizardStore, Optimization } from '@/stores/ResumeWizardStore';
import { onMounted, ref } from 'vue';

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


const breadcrumbs = ref<BreadcrumbItem[]>([
    {
        title: 'New Optimization',
        href: '/optimizations/create',
    },
]);

onMounted(() => {
    if (props.optimization) {
        state.setOptimization(props.optimization as Optimization)
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
    <Head title="New Optimization" />

    <AppLayout :breadcrumbs="breadcrumbs">

        <div v-show="state.form.status !== 'complete'" class="mx-auto flex h-full w-[950px] flex-1 flex-col gap-4 rounded-xl p-4">
            <Steps />
            <component :is="{...state.currentStep.stepComponent}" />
        </div>
        <div v-show="state.form.status === 'complete'" class="mx-auto flex h-full w-[950px] flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="bg-gray-300/10 dark:bg-[#202020] px-8 py-6">
                <h1 class="text-2xl">{{ state.form.role.company }} {{ state.form.role.name }} Application</h1>
                <h4 class="mt-4">Compatibility score: <span class="text-green-400 font-bold">95%</span> Match</h4>
                <h2 class="mt-4 font-bold">Strong Alignments:</h2>
                <div class="mt-4">
                    <h3>Tech Stack Match</h3>
                    <p class="text-gray-400">PHP 8.1+, Laravel 9+, Vue.js 3 → all match your daily toolkit
                        <br>Deep MySQL experience and backend architecture mastery
                        <br>Experience integrating frontend/backend in scalable systems</p>
                </div>
                <div class="mt-4">
                    <h3>Greenfield & eCommerce Systems</h3>
                    <p class="text-gray-400">Rebuilt payment platforms from scratch
                        <br>Experience with inventory tools, multi-gateway integrations, and real-time user features
                        <br>Can lead full-cycle projects involving multiple departments</p>
                </div>
                <div class="mt-4">
                    <h3>AI Curiosity & Innovation</h3>
                    <p class="text-gray-400">You're not in AI-heavy systems yet, but your learning mindset and backend experience make you a solid fit to support AI-powered features</p>
                </div>

                <div class="mt-4">
                    <h3>Soft Skills & Remote Collaboration</h3>
                    <p class="text-gray-400">Strong async and cross-functional communication
                        <br>Team player with proven leadership/mentoring experience</p>
                </div>
                <h2 class="mt-6 text-yellow-400 font-bold">Moderate Gaps:</h2>

                <div class="text-yellow-400">
                    <ul>
                        <li>
                            <span class="block">On-site Requirement</span>
                            <span class="block text-yellow-400/70">You're based in Brazil, while this role is **in-house in St. Thomas, Ontario</span>
                        </li>
                    </ul>
                </div>

                <hr class="my-8 max-w-xl border-t border-gray-300">

                <h2 class="font-bold">Role Specific Professional Summary</h2>
                <p class="text-gray-400">Backend Engineer with 7+ years of experience delivering robust SaaS platforms using Laravel, PHP, MySQL, and Redis. Specialized in building and maintaining scalable backend services, integrating third-party APIs, and optimizing RDBMS queries for performance and reliability. Proven success in fully remote environments, leading feature development, mentoring teams, and maintaining high standards through code review and automated testing. Adept at balancing technical debt with shipping valuable features and continuously improving integration stability across mission-critical systems.</p>
            </div>
        </div>
    </AppLayout>
</template>
