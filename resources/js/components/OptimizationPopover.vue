<script setup lang="ts">
import DeleteOptimization from '@/components/DeleteOptimization.vue'
import { Button } from '@/components/ui/button'
import { Toggle } from '@/components/ui/toggle'
import { DropdownMenu, DropdownMenuContent, DropdownMenuTrigger } from '@/components/ui/dropdown-menu'
import { SidebarMenuButton } from '@/components/ui/sidebar'
import { completeWizard } from '@/lib/axios'
import { OptimizationType, useOptimizationWizardStore } from '@/stores/OptimizationWizardStore'
import { useNavigationItemsStore } from '@/stores/NavigationItemsStore'
import { useToastsStore } from '@/stores/ToastsStore'
import { Edit, EllipsisVerticalIcon, Link, Recycle } from 'lucide-vue-next'
import { ref } from 'vue'
import { Axios } from '@/lib/axios'

const state = useOptimizationWizardStore()
const navStore = useNavigationItemsStore()
const toast = useToastsStore()
const popoverOpen = ref<boolean>(false)

const enableEdit = () => {
    state.form.status = 'editing'
    state.step = 0
    state.latestStep = 3
    popoverOpen.value = false
}

const regenerate = () => {
    state.loading = true
    popoverOpen.value = false

    completeWizard(state).then((response) => {
        state.setOptimization(response.data.optimization)
        state.form.status = 'complete'
        toast.success('Complete Optimization', 'The optimization was successfully re-generated.')
    })
}

const toggleApplied = async () => {
    try {
        const optimizationId = state.form.optimizationId
        if (!optimizationId) return

        const response = await Axios().put(route('optimizations.toggle-applied', optimizationId))
        if (response.data.success) {
            state.form.applied = response.data.applied

            // Update the navigation items store - this will trigger the broadcast
            navStore.replace({
                id: optimizationId,
                role_company: state.form.role.company,
                status: state.form.status,
                applied: response.data.applied,
                ai_response: state.form.response,
            } as OptimizationType)

            toast.success(state.form.applied ? 'Applied' : 'Not Applied', `Successfully set as ${state.form.applied ? 'applied' : 'not applied'}!`)
        }
    } catch (error) {
        console.error('Error toggling applied status:', error)
        toast.error('Error', 'Failed to update application status')
    } finally {
        popoverOpen.value = false
        state.loading = false
    }
}
</script>

<template>
    <div class="absolute right-2 top-3" v-show="!state.loading">
        <DropdownMenu v-if="state.form.optimizationId" v-model:open="popoverOpen">
            <DropdownMenuTrigger as-child @click="popoverOpen = false">
                <SidebarMenuButton size="lg" class="px-4 data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground">
                    <EllipsisVerticalIcon class="ml-auto size-4" />
                </SidebarMenuButton>
            </DropdownMenuTrigger>
            <DropdownMenuContent
                class="w-[--radix-dropdown-menu-trigger-width] min-w-56 rounded-lg"
                side="right"
                align="start"
                :side-offset="4"
                update-position-strategy="always"
            >
                <div class="flex flex-col items-stretch gap-2">
                    <Button
                        text-position="left"
                        v-if="state.form.role.url"
                        target="_blank"
                        as="a"
                        :href="state.form.role.url"
                        @click="popoverOpen = false"
                    >
                        <Link /> Apply
                    </Button>
                    <!-- Applied Toggle -->
                    <div class="flex items-center justify-between px-3 py-2">
                        <Toggle :model-value="state.form.applied" @update:model-value="toggleApplied" :disabled="state.loading">
                            <span
                                class="text-sm"
                                :class="state.form.applied ? 'text-green-600 dark:text-green-400' : 'text-gray-600 dark:text-gray-400'"
                            >
                                {{ state.form.applied ? 'Applied' : 'Not Applied' }}
                            </span>
                        </Toggle>
                    </div>

                    <Button
                        text-position="left"
                        :disabled="state.loading"
                        :variant="state.loading ? 'ghost' : 'outline'"
                        type="button"
                        size="lg"
                        @click="regenerate"
                    >
                        <Recycle /> Regenerate
                    </Button>
                    <Button
                        text-position="left"
                        :disabled="state.loading"
                        :variant="state.loading ? 'ghost' : 'outline'"
                        type="button"
                        size="lg"
                        @click="enableEdit"
                    >
                        <Edit /> Edit
                    </Button>
                    <DeleteOptimization text-position="left" />
                </div>
            </DropdownMenuContent>
        </DropdownMenu>
    </div>
</template>
