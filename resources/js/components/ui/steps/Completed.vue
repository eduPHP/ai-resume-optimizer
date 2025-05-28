<script setup lang="ts">
import { CheckCircle } from 'lucide-vue-next';
import { Buttons } from '@/components/ui/steps';
import { useResumeWizardStore } from '@/stores/ResumeWizardStore';
import { usePage } from '@inertiajs/vue3';
import { completeWizard } from '@/lib/axios';

const state = useResumeWizardStore()

const finish = () => {
    completeWizard(usePage(), state).then(response => {
        console.log('completed', response)
        state.setOptimization(response.data.optimization)
        state.form.status = 'completed'
    })
}

</script>

<template>
    <div class="bg-gray-300/10 dark:bg-[#202020] px-8 py-6">
        <div class="flex flex-col items-center gap-8">
            <h3 class="text-xl">Complete</h3>
            <CheckCircle class="text-green-500 h-16 w-16" />
            <p>That’s all the information we need!</p>
            <div class="text-center">
                <p>By clicking “Finish”, you agree to store your information and that it might be used to train LLM models.</p>
                <p>Your resume will be processed and will soon be available for download.</p>
                <p>If it takes too long, be sure to have browser notifications allowed to be notified.</p>
            </div>
        </div>

    </div>

    <Buttons :action="finish" />
</template>
