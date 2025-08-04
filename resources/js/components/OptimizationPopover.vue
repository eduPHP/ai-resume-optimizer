<script setup lang="ts">
import DeleteOptimization from '@/components/DeleteOptimization.vue'
import { Button } from '@/components/ui/button'
import { DropdownMenu, DropdownMenuContent, DropdownMenuTrigger } from '@/components/ui/dropdown-menu'
import { SidebarMenuButton } from '@/components/ui/sidebar'
import { completeWizard } from '@/lib/axios'
import { useOptimizationWizardStore } from '@/stores/OptimizationWizardStore'
import { useToastsStore } from '@/stores/ToastsStore'
import { Edit, EllipsisVerticalIcon, Link, Recycle } from 'lucide-vue-next'
import { ref } from 'vue'

const state = useOptimizationWizardStore()
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
