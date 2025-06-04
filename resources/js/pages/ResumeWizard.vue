<script setup lang="ts">
import { Steps } from '../components/ui/steps';
import { useOptimizationWizardStore } from '@/stores/OptimizationWizardStore';

const state = useOptimizationWizardStore()

</script>

<template>
    <div class="mx-auto flex h-full w-[950px] flex-1 flex-col gap-4 rounded-xl p-4">
        <Steps />
        <component :is="{...state.currentStep.stepComponent}" />
    </div>
</template>
