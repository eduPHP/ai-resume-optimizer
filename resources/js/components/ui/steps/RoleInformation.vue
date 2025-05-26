<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { useResumeWizardStore } from '@/stores/ResumeWizardStore';
import { Buttons } from '@/components/ui/steps';
import { createOrUpdateOptimization } from '@/lib/axios';
import { usePage } from '@inertiajs/vue3';

const state = useResumeWizardStore()


const submit = () => {
    createOrUpdateOptimization(usePage(), state)
}

</script>

<template>
    <div class="bg-gray-300/10 dark:bg-[#202020] rounded-md px-8 py-6">
        <h3 class="text-xl mb-8">Role Information</h3>
        <p class="text-gray-400  mb-8">A resume should be role specific, <br>Please provide the role information to optimize the resume for the role you are applying for.</p>
        <div class="mb-8 grid grid-cols-2 items-start gap-4">
            <div class="grid gap-2">
                <Label for="name">Role Name</Label>
                <Input id="name" type="text" :autofocus="!!state.form.errors.name" :tabindex="1" v-model="state.form.role.name" @input="state.clearErrors('name')" />
                <span v-show="! state.form.errors.name" class="text-xs text-white/50">i.e. Backend Engineer</span>
                <InputError :message="state.form.errors.name" />
            </div>
            <div class="grid gap-2">
                <Label for="company">Company Name</Label>
                <Input id="company" type="text" :autofocus="!!state.form.errors.company" :tabindex="2" v-model="state.form.role.company" @input="state.clearErrors('company')" />
                <InputError :message="state.form.errors.company" />
            </div>
        </div>

        <div class="grid gap-2">
            <Label for="description">Role Description</Label>
            <Textarea id="description" type="text" :autofocus="!!state.form.errors.description" :tabindex="3" v-model="state.form.role.description" @input="state.clearErrors('description')" />
            <InputError :message="state.form.errors.description" />
        </div>

    </div>

    <Buttons :action="submit" />
</template>
