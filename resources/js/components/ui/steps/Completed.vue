<script setup lang="ts">
import { CheckCircle } from 'lucide-vue-next';
import { Buttons } from '@/components/ui/steps';
import { useOptimizationWizardStore } from '@/stores/OptimizationWizardStore';
import { usePage } from '@inertiajs/vue3';
import { completeWizard } from '@/lib/axios';
import { useToastsStore } from '@/stores/ToastsStore';

const state = useOptimizationWizardStore()
const toast = useToastsStore();

const finish = () => {
    completeWizard(usePage(), state).then(response => {
        state.setOptimization(response.data.optimization)
        state.form.status = 'complete'
        toast.success('Complete Optimization', 'The optimization was successfully re-generated.')
    })
}

</script>

<template>
    <div class="bg-gray-300/10 dark:bg-[#202020] px-8 py-6 min-w-80">
        <div class="flex flex-col items-center gap-8">
            <h3 class="hidden xl:block text-xl">Complete</h3>
            <CheckCircle class="text-green-500 h-16 w-16" />
            <p>That’s all the information we need!</p>
            <div class="text-center">
                <p>By clicking “Optimize”, you agree to store your information and that it might be used to train LLM models.</p>
                <p>Your resume will be processed and will soon be available for download.</p>
                <p>If it takes too long, be sure to have browser notifications allowed to be notified.</p>
            </div>
        </div>

    </div>

    <Buttons :action="finish" />
</template>
