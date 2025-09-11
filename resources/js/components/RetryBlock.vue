<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { Edit, Repeat } from 'lucide-vue-next'
import DeleteOptimization from '@/components/DeleteOptimization.vue'
import { OptimizationType, useOptimizationWizardStore } from '@/stores/OptimizationWizardStore'
import { createUnattendedOptimization } from '@/lib/axios'
import { router } from '@inertiajs/vue3'
import { useNavigationItemsStore } from '@/stores/NavigationItemsStore'
import { format } from 'date-fns'

const state = useOptimizationWizardStore()
const nav = useNavigationItemsStore()

const retry = () => {
    state.form.status = 'processing'
    createUnattendedOptimization().then((response) => {
        state.setOptimization(response.data.optimization as OptimizationType)
        // force override to hide retry
        state.form.updated = format(new Date(), 'Pp')
        nav.replace(response.data.optimization)
        router.visit(route('optimizations.show', response.data.optimization.id), { preserveState: true })
    })
}

const edit = () => {
    state.step = 0
    state.form.status = 'editing'
}
</script>

<template>
    <div class="pt-6 flex flex-col justify-center items-center">
        <Button
            type="button"
            size="lg"
            class="min-w-40 select-none xl:flex-none"
            :class="{ 'cursor-not-allowed': state.loading }"
            :disabled="state.loading"
            @click.prevent="retry"
        >
            <Repeat /> Retry
        </Button>
        <div class="mt-4">
            <DeleteOptimization />

            <Button
                v-if="state.form.response.compatibility_score"
                :disabled="state.loading"
                type="button"
                class="!h-10 select-none text-gray-400 dark:text-gray-300 xl:flex-none"
                :class="{ 'cursor-not-allowed': state.loading }"
                variant="link"
                @click.prevent="edit"
            >
                <Edit /> Edit
            </Button>
        </div>
    </div>
</template>
