<script setup lang="ts">
import { useOptimizationWizardStore } from '@/stores/OptimizationWizardStore';
import { Button } from '@/components/ui/button';

defineProps<{
    action: () => void
}>()

const state = useOptimizationWizardStore()

</script>
<template>
    <div class="flex justify-end items-center gap-4">
        <Button type="button"
                variant="ghost"
                v-if="state.step > 0"
                :disabled="state.loading"
                @click.prevent="state.prevStep"
        >Prev</Button>
        <Button v-if="state.step < state.steps.length - 1"
                :disabled="state.loading"
                type="button"
                @click.prevent="action"
        >Next</Button>
        <Button type="button"
                v-if="state.step === state.steps.length - 1"
                :disabled="state.loading"
                @click.prevent="action"
        >Optimize</Button>
    </div>
</template>
