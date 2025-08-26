<script setup lang="ts">
import { CheckCircle } from 'lucide-vue-next';
import { Buttons } from '@/components/ui/steps';
import { OptimizationType, useOptimizationWizardStore } from '@/stores/OptimizationWizardStore';
import { completeWizard } from '@/lib/axios';
import { useToastsStore } from '@/stores/ToastsStore';
import { useNavigationItemsStore } from '@/stores/NavigationItemsStore';
import Heading from '@/components/Heading.vue';

const state = useOptimizationWizardStore()
const toast = useToastsStore()
const nav = useNavigationItemsStore()

const finish = () => {
    state.form.status = 'pending'

    completeWizard(state).then(response => {
        state.setOptimization(response.data.optimization)
        state.form.status = 'complete'
        toast.success('Complete', 'Resume optimization was successfully completed.')
        nav.replace({
            id: response.data.optimization.id,
            role_company: response.data.optimization.role_company,
            status: response.data.optimization.status,
            applied: response.data.optimization.applied,
            ai_response: response.data.optimization.ai_response,
        } as OptimizationType)
    })
}

</script>

<template>
    <div class="bg-gray-300/10 dark:bg-[#202020] px-8 py-6 min-w-80">
        <div class="flex flex-col items-center gap-8">
            <Heading title="Complete" />

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
