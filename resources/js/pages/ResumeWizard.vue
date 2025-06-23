<script setup lang="ts">
import { Steps } from '../components/ui/steps';
import { useOptimizationWizardStore } from '@/stores/OptimizationWizardStore';
import { useBreakpoints } from '@/composables/useBreakpoints';

const state = useOptimizationWizardStore()
const { breakpoint } = useBreakpoints()

</script>

<template>
    <div class="mx-auto flex h-full w-full xl:w-[950px] min-w-80 flex-1 flex-col gap-4 rounded-xl p-4">
        <Steps />
        <template v-if="['xl', '2xl'].includes(breakpoint)">
            <!-- /* only include the step in this position on larger screens */ -->
            <component :is="{...state.currentStep.stepComponent}" />
        </template>
    </div>
</template>
