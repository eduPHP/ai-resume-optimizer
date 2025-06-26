<script setup lang="ts">
import { Toggle } from '@/components/ui/toggle';
import { Input } from '@/components/ui/input';
import InputError from '@/components/InputError.vue';
import { Label } from '@/components/ui/label';
import { useOptimizationWizardStore } from '@/stores/OptimizationWizardStore';
import { Buttons } from '@/components/ui/steps';
import { updateAdditionalInformation } from '@/lib/axios';
import { watch } from 'vue';

const state = useOptimizationWizardStore()

const submit = () => {
    updateAdditionalInformation(state)
}

watch(() => state.form.additional.targetCountry, (country: string) => {
    state.form.role.location = country
})

</script>

<template>
    <div class="bg-gray-300/10 dark:bg-[#202020] px-8 py-6 w-full min-w-80">
        <h3 class="hidden xl:block text-xl mb-6">Additional Information</h3>
        <p class="text-gray-400">Select the options that match your goal</p>
        <div class="py-6 grid xl:grid-cols-2 gap-6">
            <Toggle v-model="state.form.additional.makeGrammaticalCorrections">
                Make grammatical corrections
            </Toggle>
            <Toggle v-model="state.form.additional.changeProfessionalSummary">
                Change Professional Summary
            </Toggle>
            <Toggle v-model="state.form.additional.generateCoverLetter">
                Generate a cover letter
            </Toggle>
            <Toggle v-model="state.form.additional.changeTargetRole">
                Change Target Role
            </Toggle>
            <Toggle v-model="state.form.additional.mentionRelocationAvailability">
                Mention relocation availability
            </Toggle>
        </div>
        <div class="mt-4 grid gap-2 xl:w-1/2">
            <Label for="country">Target Country/City</Label>
            <Input id="country" type="text" required :tabindex="1" v-model="state.form.additional.targetCountry" />
            <InputError :message="state.form.errors.targetCountry" />
        </div>

    </div>

    <Buttons :action="submit" />
</template>
