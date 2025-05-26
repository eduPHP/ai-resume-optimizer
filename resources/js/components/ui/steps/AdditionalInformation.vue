<script setup lang="ts">
import { Toggle } from '@/components/ui/toggle';
import { Input } from '@/components/ui/input';
import InputError from '@/components/InputError.vue';
import { Label } from '@/components/ui/label';
import { useForm } from '@inertiajs/vue3';
import { useResumeWizardStore, AdditionalInformationForm } from '@/stores/ResumeWizardStore';
import { Buttons } from '@/components/ui/steps';

const state = useResumeWizardStore()

const form = useForm<AdditionalInformationForm>(state.form.additional)

const submit = () => {
    state.loading = true
    form.post(route('optimizations.store'), {
        headers: {
            'X-CurrentStep': state.step.toString(),
        },
        onSuccess: () => {
            state.form.additional = form.data()
            state.nextStep()
        },
        onFinish: () => {
            state.loading = false
        }
    });
}

</script>

<template>
    <div class="bg-gray-300/10 dark:bg-[#202020] px-8 py-6">
        <h3 class="text-xl mb-6">Additional Information</h3>
        <p class="text-gray-400">Select the options that match your goal</p>
        <div class="py-6 grid grid-cols-2 gap-6">
            <Toggle v-model="form.makeGrammaticalCorrections">
                Make grammatical corrections
            </Toggle>
            <Toggle v-model="form.changeProfessionalSummary">
                Change Professional Summary
            </Toggle>
            <Toggle v-model="form.changeTargetRole">
                Change Target Role
            </Toggle>
            <Toggle v-model="form.mentionRelocationAvailability">
                Mention relocation availability
            </Toggle>
        </div>
        <div class="mt-4 grid gap-2 w-1/2">
            <Label for="country">Target Country</Label>
            <Input id="country" type="text" required autofocus :tabindex="1" v-model="form.targetCountry" />
            <InputError :message="form.errors.targetCountry" />
        </div>

    </div>

    <Buttons :action="submit" />
</template>
