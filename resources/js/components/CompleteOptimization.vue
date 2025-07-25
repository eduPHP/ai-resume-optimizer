<script setup lang="ts">

import { downloadCoverLetter, downloadOptimizedResume } from '@/lib/axios';
import { Button } from '@/components/ui/button';
import { File } from 'lucide-vue-next';
import { usePage } from '@inertiajs/vue3';
import { useOptimizationWizardStore } from '@/stores/OptimizationWizardStore';
import OptimizationPopover from '@/components/OptimizationPopover.vue';
import { onMounted } from 'vue';
import { SharedData } from '@/types';

const state = useOptimizationWizardStore()
const page = usePage<SharedData>();
onMounted(() => {
    state.setAISettings(page.props.auth?.user.ai_settings)
})

</script>

<template>
    <div class="mx-auto flex h-full w-full xl:w-[950px] flex-1 flex-col gap-4 rounded-xl p-4">
        <div class="relative bg-gray-300/10 dark:bg-[#202020] px-8 py-6">
            <h1 class="text-2xl">{{ state.form.role.company }} {{ state.form.role.name }} Application</h1>

            <h2 class="mt-4 text-xl">Compatibility score: <span class="font-bold" :class="state.compatibilityStyle">{{state.form.response.compatibility_score}}%</span> Match</h2>
            <p class="text-gray-600 dark:text-gray-400">{{ state.form.response.reasoning }}</p>
            <div v-if="state.form.response.top_choice?.length">
                <hr class="my-8 mx-auto max-w-xl border-t border-gray-300 dark:border-gray-500">
                <h2 class="mt-4 text-xl">Top Choice Message</h2>
                <p class="text-gray-600 dark:text-gray-400">{{ state.form.response.top_choice }}</p>
            </div>

            <hr class="my-8 mx-auto max-w-xl border-t border-gray-300 dark:border-gray-500">

            <h2 class="mt-6 text-xl">Role Specific Professional Summary</h2>
            <p class="text-gray-600 dark:text-gray-400">{{ state.form.response.professional_summary }}</p>

            <hr class="my-8 mx-auto max-w-xl border-t border-gray-300 dark:border-gray-500">

            <h2 class="mt-4 text-xl">Alignments:</h2>
            <div v-for="alignment in state.form.response.strong_alignments" :key="alignment.title" class="mt-4">
                <h3>{{alignment.title}}</h3>
                <p class="text-gray-600 dark:text-gray-400">{{ alignment.description }}</p>
            </div>

            <h2 v-if="state.form.response.moderate_gaps.length" class="mt-6 text-xl text-yellow-400">Moderate Gaps:</h2>

            <div v-if="state.form.response.moderate_gaps.length" class="text-yellow-400">
                <ul>
                    <li v-for="gap in state.form.response.moderate_gaps" :key="gap.title" >
                        <span class="block">{{ gap.title }}</span>
                        <span class="block text-yellow-400/70">{{ gap.description }}</span>
                    </li>
                </ul>
            </div>

            <h2 v-if="state.form.response.missing_requirements.length" class="mt-6 text-red-400 text-xl">Missing Requirements:</h2>

            <div v-if="state.form.response.missing_requirements.length" class="text-red-400">
                <ul>
                    <li v-for="missing in state.form.response.missing_requirements" :key="missing.title" >
                        <span class="block">{{ missing.title }}</span>
                        <span class="block text-red-400/70">{{ missing.description }}</span>
                    </li>
                </ul>
            </div>

            <hr class="my-8 mx-auto max-w-xl border-t border-gray-300 dark:border-gray-500">

            <h2 v-if="state.form.response.cover_letter?.length" class="mt-6 text-xl">Cover Letter:</h2>
            <div v-if="state.form.response.cover_letter?.length" class="mt-4 mb-4">
                <p class="text-gray-600 dark:text-gray-400">Dear Hiring manager,</p>
                <p v-for="(paragraph, index) in state.form.response.cover_letter ?? []" :key="`${paragraph}-${index}`" class="py-2 text-gray-600 dark:text-gray-400">{{ paragraph }}</p>
                <p>Regards,<br>{{page.props.auth?.user.name}}</p>
            </div>

            <OptimizationPopover class="absolute right-2 top-3" />
        </div>

        <div class="flex flex-col-reverse xl:flex-row justify-end gap-2 pb-8 xl:pb-0">
            <Button :disabled="state.loading || ! state.form.response.cover_letter?.length" :variant="state.loading || ! state.form.response.cover_letter?.length ? 'ghost' : 'outline'" type="button" size="lg" @click="downloadCoverLetter(state)">
                <File />
                Download Cover Letter
            </Button>
            <Button :disabled="state.loading" type="button" size="lg" @click="downloadOptimizedResume(state)">
                <File />
                Download Optimized Resume
            </Button>
        </div>
    </div>
</template>
