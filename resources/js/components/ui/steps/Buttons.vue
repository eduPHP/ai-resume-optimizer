<script setup lang="ts">
import { useResumeWizardStore } from '@/stores/ResumeWizardStore';
import { cn } from '@/lib/utils';

defineProps({
    action: {
        required: true,
        type: Function,
    },
})
const state = useResumeWizardStore()

</script>
<template>
    <div class="flex justify-end items-center gap-4">
        <button type="button"
                :class="cn(
                    'bg-gray-300/10 dark:bg-[#202020] rounded-sm px-4 py-2',
                    state.loading ? 'opacity-50' : ''
                )"
                v-if="state.step > 0"
                :disabled="state.loading"
                @click.prevent="state.prevStep"
        >Prev</button>
        <button type="button"
                :class="cn(
                    'bg-gray-300/10 dark:bg-[#202020] rounded-sm px-4 py-2',
                    state.loading ? 'opacity-50' : ''
                )"
                v-if="state.step < state.steps.length - 1"
                :disabled="state.loading"
                @click.prevent="action"
        >Next</button>
        <button type="button"
                :class="cn(
                    'bg-gray-300/10 dark:bg-[#202020] rounded-sm px-4 py-2',
                    state.loading ? 'opacity-50' : ''
                )"
                v-if="state.step === state.steps.length - 1"
                :disabled="state.loading"
                @click.prevent="action"
        >Finish</button>
    </div>
</template>
