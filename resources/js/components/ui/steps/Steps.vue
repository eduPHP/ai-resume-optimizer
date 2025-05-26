<script setup lang="ts">
import { CheckIcon } from '@heroicons/vue/20/solid'
import { useResumeWizardStore } from '@/stores/ResumeWizardStore';

let state = useResumeWizardStore()

</script>
<template>
    <nav aria-label="Progress">
        <ol role="list" class="flex items-center justify-between">
            <li v-for="(step, stepIdx) in state.steps" :key="step.name" :class="[stepIdx !== state.steps.length - 1 ? 'pr-8 sm:pr-20 w-full' : '', 'relative']">
                <div class="flex-col items-center justify-center" v-if="state.step > stepIdx || (state.step === stepIdx && stepIdx === state.steps.length - 1)">
                    <div class="absolute inset-0 flex items-center" aria-hidden="true">
                        <div class="ml-12 h-0.5 w-full border-t border-green-500 border-dashed" />
                    </div>
                    <button type="button" @click="state.setStep(stepIdx)" class="relative flex size-12 items-center justify-center rounded-full border-green-600 border hover:border-green-700 text-green-600 hover:text-green-700">
                        <CheckIcon class="size-5" aria-hidden="true" />
                        <span class="sr-only">{{ step.name }}</span>
                    </button>
                </div>
                <template v-else-if="state.step === stepIdx">
                    <div class="absolute inset-0 flex items-center" aria-hidden="true">
                        <div class="ml-12 h-0.5 w-full border-t border-gray-400 dark:border-white/40 border-dashed" />
                    </div>
                    <button type="button" @click="state.setStep(stepIdx)" class="relative flex size-12 items-center justify-center rounded-full border-2 dark:border-white dark:hover:border-white/80 border-gray-800 hover:border-gray-500" aria-current="step">
                        <span class="dark:text-white select-none">{{ stepIdx + 1 }}</span>
                        <span class="sr-only">{{ step.name }}</span>
                    </button>
                </template>
                <template v-else>
                    <div class="absolute inset-0 flex items-center" aria-hidden="true">
                        <div class="ml-12 h-0.5 w-full border-t border-gray-400 dark:border-white/40 border-dashed" />
                    </div>
                    <button type="button" @click="state.setStep(stepIdx)" class="group relative flex size-12 items-center justify-center rounded-full border dark:border-white/40 dark:hover:border-white/80 border-gray-400 hover:border-gray-500">
                        <span class="text-gray-400 dark:text-white/40">{{ stepIdx + 1 }}</span>
                        <span class="sr-only">{{ step.name }}</span>
                    </button>
                </template>
            </li>
        </ol>
    </nav>
</template>
