<script setup lang="ts">
import { useBreakpoints } from '@/composables/useBreakpoints'
import { useOptimizationWizardStore } from '@/stores/OptimizationWizardStore'
import { Steps } from './ui/steps'

const state = useOptimizationWizardStore()
const { breakpoint } = useBreakpoints()
</script>

<template>
    <div class="mx-auto flex h-full w-full min-w-80 flex-1 flex-col gap-4 rounded-xl p-4 xl:w-[950px]">
        <Steps />
        <template v-if="['xl', '2xl'].includes(breakpoint)">
            <!-- /* only include the step in this position on larger screens */ -->
            <component :is="{ ...state.currentStep.stepComponent }" />
        </template>
    </div>
</template>
