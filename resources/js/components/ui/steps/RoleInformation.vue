<script setup lang="ts">
import { LoaderCircle } from 'lucide-vue-next';
import InputError from '@/components/InputError.vue';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { OptimizationType, useOptimizationWizardStore } from '@/stores/OptimizationWizardStore';
import { Buttons } from '@/components/ui/steps'
import { createOrUpdateOptimization, getJobInformation, createUnattendedOptimization } from '@/lib/axios';
import { router } from '@inertiajs/vue3';
import { useNavigationItemsStore } from '@/stores/NavigationItemsStore';
import debounce from '@/lib/debounce';
import Heading from '@/components/Heading.vue';
import { ref } from 'vue'
import { useToastsStore } from '@/stores/ToastsStore'
import { Button } from '@/components/ui/button'

const state = useOptimizationWizardStore()
const nav = useNavigationItemsStore()
const toast = useToastsStore()

const showComplete = ref(false)


const submit = async () => {
    return createOrUpdateOptimization().then(response => {
        if (response.data.created) {
            state.setOptimization(response.data.optimization as OptimizationType)
            nav.addItem(response.data.optimization.role_company, response.data.optimization.id as string, true)
            router.visit(route('optimizations.show', response.data.optimization.id), { preserveState: true })
        }
    })
}

const finish = () => {
    createUnattendedOptimization().then(response => {
        state.setOptimization(response.data.optimization as OptimizationType)
        nav.replace({
            id: response.data.optimization.id,
            role_company: response.data.optimization.role_company,
            status: response.data.optimization.status,
            applied: response.data.optimization.applied,
            ai_response: response.data.optimization.ai_response,
        } as OptimizationType)
        toast.success('Created', 'Optimization was created.')
        router.visit(route('optimizations.show', response.data.optimization.id), { preserveState: true })
    })
}

const toTitleCase = (str: string) => {
    return str.replace(
        /\w\S*/g,
        text => text.charAt(0).toUpperCase() + text.substring(1).toLowerCase()
    ).replace('Php', 'PHP')
}

const getJobInformationHandler = (url: string) => {
    if (url.trim().length === 0) {
        return
    }

    try {
        const parsed = JSON.parse(url);
        if ( typeof parsed === 'object' && parsed !== null && !Array.isArray(parsed)) {
            state.form.role.url = parsed.url;
            state.form.role.company = parsed.company
            state.form.role.name = toTitleCase(parsed.role)
            state.form.role.description = parsed.description
            state.form.additional.targetCountry = parsed.location
            state.form.role.location = parsed.location

            showComplete.value = true

            return
        }
    } catch (e) {
        // do nothing
    }

    if (!url.startsWith('http')) {
        return;
    }


    state.loading = true
    getJobInformation(url).then(response => {
        state.form.role.url = response.data.url

        if (response.data.supported) {
            state.form.role.company = response.data.company
            state.form.role.name = toTitleCase(response.data.position)
            state.form.role.description = response.data.description
            // set location for both role and additional info state
            state.form.additional.targetCountry = response.data.location
            state.form.role.location = response.data.location
            showComplete.value = true
        } else {
            console.info('Unsupported crawl link: '+url)
        }
        state.loading = false
    })
}
const debouncedGetJobInformation = debounce(() => getJobInformationHandler(state.form.role.url as string));

</script>

<template>
    <div class="bg-gray-300/10 dark:bg-[#202020] rounded-md px-8 py-6 min-w-80">
        <Heading title="Role Information" description="A resume should be role specific, <br>Please provide the role information to optimize the resume for the role you are applying for." />

        <div class="mb-8 grid gap-2">
            <Label for="url">Job Link <span class="text-yellow-500 dark:text-yellow-300 text-xs">(Experimental)</span></Label>
            <div class="relative">
                <Input :class="state.loading && 'pr-10'" id="url" type="text" :tabindex="1" v-model="state.form.role.url" @input="debouncedGetJobInformation" />
                <LoaderCircle class="animate-spin absolute right-3 top-2.5 text-gray-400" v-if="state.loading" />
            </div>

            <span v-show="! state.form.errors.url" class="text-xs text-muted-foreground dark:text-white/50">Automatically fill the role information from a job link.</span>
            <InputError :message="state.form.errors.url" />
        </div>
        <div class="mb-8 grid xl:grid-cols-2 items-start gap-4">
            <div class="grid gap-2">
                <Label for="name">Role Name</Label>
                <Input id="name" type="text" :tabindex="1" v-model="state.form.role.name" @input="state.clearErrors('name')" />
                <span v-show="! state.form.errors.name" class="text-xs text-muted-foreground dark:text-white/50">i.e. Backend Engineer</span>
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

    <Buttons :action="submit">
        <Button v-if="showComplete" type="button"
                size="lg"
                class="flex-1 xl:flex-none select-none"
                :class="{'cursor-not-allowed': state.loading}"
                :disabled="state.loading"
                @click.prevent="finish"
        >Optimize</Button>
    </Buttons>
</template>
