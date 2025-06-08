<script setup lang="ts">

import { completeWizard, downloadPDF } from '@/lib/axios';
import { Button } from '@/components/ui/button';
import { Edit, File, Recycle } from 'lucide-vue-next';
import { usePage } from '@inertiajs/vue3';
import { useOptimizationWizardStore } from '@/stores/OptimizationWizardStore';
import { onMounted, ref } from 'vue';

const state = useOptimizationWizardStore()
const page = usePage();
const compatibilityPercentageStyle = ref<string>('')

const setupOptimization = () => {
    if (state.form.status === 'complete') {
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
}

onMounted(() => {
    setupOptimization()
})
const enableEdit = () => {
    state.form.status = 'pending'
    state.step = 0
    state.latestStep = 3
}
const regenerate = () => {
    state.loading = true;

    completeWizard(page, state).then((response) => {
        state.setOptimization(response.data.optimization)
        state.form.status = 'complete'
        setupOptimization()
    })
}

</script>

<template>
    <div class="mx-auto flex h-full w-[950px] flex-1 flex-col gap-4 rounded-xl p-4">
        <div class="bg-gray-300/10 dark:bg-[#202020] px-8 py-6">

            <h1 class="text-2xl">{{ state.form.role.company }} {{ state.form.role.name }} Application</h1>

            <h4 class="mt-4">Compatibility score: <span class="font-bold" :class="compatibilityPercentageStyle">{{state.form.response.compatibility_score}}%</span> Match</h4>
            <p class="text-gray-600 dark:text-gray-400">{{ state.form.response.reasoning }}</p>

            <hr class="my-8 mx-auto max-w-xl border-t border-gray-300 dark:border-gray-500">

            <h2 class="mt-6 font-bold">Role Specific Professional Summary</h2>
            <p class="text-gray-600 dark:text-gray-400">{{ state.form.response.professional_summary }}</p>

            <hr class="my-8 mx-auto max-w-xl border-t border-gray-300 dark:border-gray-500">

            <h2 class="mt-4 font-bold">Strong Alignments:</h2>
            <div v-for="alignment in state.form.response.strong_alignments" :key="alignment.title" class="mt-4">
                <h3>{{alignment.title}}</h3>
                <p class="text-gray-600 dark:text-gray-400">{{ alignment.description }}</p>
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

            <hr class="my-8 mx-auto max-w-xl border-t border-gray-300 dark:border-gray-500">

            <h2 v-if="state.form.response.cover_letter.length" class="mt-6 font-bold">Cover Letter:</h2>
            <div class="mt-4">
                <p v-for="(paragraph, index) in state.form.response.cover_letter" :key="`${paragraph}-${index}`" class="py-2 text-gray-600 dark:text-gray-400">{{ paragraph }}</p>
                <p>Regards,<br>{{page.props.auth?.user.name}}</p>
            </div>
        </div>

        <div class="flex justify-end gap-2">
            <Button :disabled="state.loading" :variant="state.loading ? 'ghost' : 'outline'" type="button" @click="regenerate">
                <Recycle /> Regenerate
            </Button>
            <Button :disabled="state.loading" :variant="state.loading ? 'ghost' : 'outline'" type="button" @click="enableEdit">
                <Edit /> Edit
            </Button>
            <Button :disabled="state.loading" type="button" @click="downloadPDF(page, state)">
                <File />
                Download Optimized Resume
            </Button>
        </div>
    </div>
</template>
