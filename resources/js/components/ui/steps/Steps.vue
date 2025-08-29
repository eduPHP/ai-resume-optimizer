<script setup lang="ts">
import { CheckIcon } from '@heroicons/vue/20/solid'
import { useOptimizationWizardStore } from '@/stores/OptimizationWizardStore';
import { useBreakpoints } from '@/composables/useBreakpoints';

let state = useOptimizationWizardStore()
const { breakpoint } = useBreakpoints()

</script>
<template>
    <nav aria-label="Progress">
        <ol role="list" class="flex flex-col gap-6 xl:gap-0 xl:flex-row xl:items-center xl:justify-between">
            <li v-for="(step, stepIdx) in state.steps" :key="step.name" :class="[stepIdx !== state.steps.length - 1 ? 'xl:pr-20 w-full' : '', 'relative']">
                <template v-if="state.step > stepIdx || (state.step === stepIdx && stepIdx === state.steps.length - 1)">
                    <div class="hidden absolute inset-0 xl:flex items-center" aria-hidden="true">
                        <div class="ml-12 h-0.5 w-full border-t border-green-500 border-dashed" />
                    </div>
                    <button @click="state.setStep(stepIdx)" class="group w-full flex gap-6 items-center text-green-600 hover:text-green-700 cursor-pointer" :class="{'dark:bg-[#202020] rounded-tl-2xl': state.step === stepIdx && stepIdx === state.steps.length - 1 && !['xl', '2xl'].includes(breakpoint)}">
                        <span type="button" class="relative flex size-12 items-center justify-center rounded-full border-green-600 border group-hover:border-green-700 ">
                            <CheckIcon class="size-5" aria-hidden="true" />
                            <span class="sr-only">{{ step.name }}</span>
                        </span>
                        <span class="xl:hidden">{{ step.name }}</span>
                    </button>

                    <template v-if="state.currentStep && state.step === stepIdx && stepIdx === state.steps.length - 1 && !['xl', '2xl'].includes(breakpoint)">
                        <!-- /* include the step in this position on mobile */ -->
                        <component :is="{...state.currentStep.stepComponent}" />
                    </template>
                </template>
                <template v-else-if="state.step === stepIdx && !['xl', '2xl'].includes(breakpoint)">
                    <div class="hidden absolute inset-0 xl:flex items-center" aria-hidden="true">
                        <div class="ml-12 h-0.5 w-full border-t border-gray-800 dark:border-white/40 border-dashed" />
                    </div>

                    <button type="button" @click="state.setStep(stepIdx)" class="w-full group flex gap-6 items-center dark:text-white hover:text-white/80 cursor-pointer" :class="{'dark:bg-[#202020] rounded-tl-2xl':!['xl', '2xl'].includes(breakpoint)}" aria-current="step">
                        <span class="relative flex size-12 items-center justify-center rounded-full border-gray-800 dark:border-white border dark:group-hover:border-white/80 ">
                            <span class="">{{ stepIdx + 1 }}</span>
                            <span class="sr-only">{{ step.name }}</span>
                        </span>
                        <span class="xl:hidden">{{ step.name }}</span>
                    </button>

                    <template v-if="!['xl', '2xl'].includes(breakpoint)">
                        <component :is="{...state.currentStep.stepComponent}" />
                    </template>
                </template>
                <template v-else>
                    <div class="hidden absolute inset-0 xl:flex items-center" aria-hidden="true">
                        <div class="ml-12 h-0.5 w-full border-t border-gray-400 dark:border-white/40 border-dashed" />
                    </div>

                    <button type="button" @click="state.setStep(stepIdx)" class="group flex gap-6 items-center text-gray-400 dark:text-white/40 dark:hover:text-white/80 cursor-pointer" aria-current="step">
                        <span class="relative flex size-12 items-center justify-center rounded-full border-gray-300 dark:border-white/40 border dark:group-hover:border-white/80 ">
                            <span class="">{{ stepIdx + 1 }}</span>
                            <span class="sr-only">{{ step.name }}</span>
                        </span>
                        <span class="xl:hidden">{{ step.name }}</span>
                    </button>
                </template>
            </li>
        </ol>
    </nav>
</template>
