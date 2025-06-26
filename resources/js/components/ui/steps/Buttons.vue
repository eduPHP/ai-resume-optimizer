<script setup lang="ts">
import { useOptimizationWizardStore } from '@/stores/OptimizationWizardStore';
import { Button } from '@/components/ui/button';
import DeleteOptimization from '@/components/DeleteOptimization.vue';
import { cancelOptimizationEdit } from '@/lib/axios';
import { X } from 'lucide-vue-next';

defineProps<{
    action: () => void
}>()

const state = useOptimizationWizardStore()

const cancelEdit = () => {
    cancelOptimizationEdit(state)
}

</script>
<template>
    <div class="mt-2 xl:mt-0 flex flex-col-reverse xl:flex-row xl:justify-end xl:items-center gap-2 xl:gap-4">
        <div class="flex-1 flex gap-4 justify-start" v-if="state.step > 0 || state.form.status === 'editing'">
            <DeleteOptimization />

            <Button v-if="state.form.response.compatibility_score"
                    :disabled="state.loading"
                    type="button"
                    class="flex-1 xl:flex-none"
                    variant="outline"
                    @click.prevent="cancelEdit"
            ><X /> Cancel Edit</Button>
        </div>

        <Button type="button"
                size="lg"
                class="flex-1 xl:flex-none"
                variant="outline"
                v-if="state.step > 0"
                :disabled="state.loading"
                @click.prevent="state.prevStep"
        >Prev</Button>
        <Button v-if="state.step < state.steps.length - 1"
                :disabled="state.loading"
                type="button"
                size="lg"
                class="flex-1 xl:flex-none"
                @click.prevent="action"
        >Next</Button>
        <Button type="button"
                size="lg"
                class="flex-1 xl:flex-none"
                v-if="state.step === state.steps.length - 1"
                :disabled="state.loading"
                @click.prevent="action"
        >Optimize</Button>
    </div>
</template>
