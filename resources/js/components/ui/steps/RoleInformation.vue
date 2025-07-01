<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { OptimizationType, useOptimizationWizardStore } from '@/stores/OptimizationWizardStore';
import { Buttons } from '@/components/ui/steps';
import { createOrUpdateOptimization, getJobInformation } from '@/lib/axios';
import { router } from '@inertiajs/vue3';
import { useNavigationItemsStore } from '@/stores/NavigationItemsStore';

const state = useOptimizationWizardStore()
const nav = useNavigationItemsStore()

const submit = () => {
    createOrUpdateOptimization().then(response => {
        if (response.data.created) {
            state.setOptimization(response.data.optimization as OptimizationType)
            nav.addItem(response.data.optimization.role_company, response.data.optimization.id as string, true)
            router.visit(route('optimizations.show', response.data.optimization.id), { preserveState: true })
        }
    })
}

const getJobInformationHandler = (event: Event) => {
    const url = (event.target as HTMLInputElement).value

    if (url.trim().length === 0) {
        return;
    }

    if (!url.startsWith('http')) {
        return;
    }

    getJobInformation(url).then(response => {
        state.form.role.company = response.data.company
        state.form.role.name = response.data.position
        state.form.role.description = response.data.description
        state.form.role.location = response.data.location
        state.form.additional.targetCountry = response.data.location

        submit()
    })
}


</script>

<template>
    <div class="bg-gray-300/10 dark:bg-[#202020] rounded-md px-8 py-6 min-w-80">
        <h3 class="hidden xl:block text-xl mb-8">Role Information</h3>
        <p class="text-gray-400  mb-8">A resume should be role specific, <br>Please provide the role information to optimize the resume for the role you are applying for.</p>

        <div class="mb-8 grid gap-2">
            <Label class="flex justify-between" for="name">Job Link <span class="text-yellow-300 text-xs">Experimental</span></Label>
            <Input id="name" type="text" :tabindex="1" v-model="state.form.role.url" @input="getJobInformationHandler" />
            <span v-show="! state.form.errors.url" class="text-xs text-white/50">Automatically fill the role information from a job link, currently works for Linkedin.</span>
            <InputError :message="state.form.errors.url" />
        </div>
        <div class="mb-8 grid xl:grid-cols-2 items-start gap-4">
            <div class="grid gap-2">
                <Label for="name">Role Name</Label>
                <Input id="name" type="text" :tabindex="1" v-model="state.form.role.name" @input="state.clearErrors('name')" />
                <span v-show="! state.form.errors.name" class="text-xs text-white/50">i.e. Backend Engineer</span>
                <InputError :message="state.form.errors.name" />
            </div>
            <div class="grid gap-2">
                <Label for="company">Company Name</Label>
                <Input id="company" type="text" :tabindex="2" v-model="state.form.role.company" @input="state.clearErrors('company')" />
                <InputError :message="state.form.errors.company" />
            </div>
        </div>

        <div class="grid gap-2">
            <Label for="description">Role Description</Label>
            <Textarea id="description" type="text" :tabindex="3" v-model="state.form.role.description" @input="state.clearErrors('description')" />
            <InputError :message="state.form.errors.description" />
        </div>

    </div>

    <Buttons :action="submit" />
</template>
