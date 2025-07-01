<script setup lang="ts">

import { Edit, EllipsisVerticalIcon, Link, Recycle } from 'lucide-vue-next';
import { DropdownMenu, DropdownMenuContent, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Button } from '@/components/ui/button';
import DeleteOptimization from '@/components/DeleteOptimization.vue';
import { SidebarMenuButton } from '@/components/ui/sidebar';
import { completeWizard } from '@/lib/axios';
import { useToastsStore } from '@/stores/ToastsStore';
import { useOptimizationWizardStore } from '@/stores/OptimizationWizardStore';

const state = useOptimizationWizardStore()
const toast = useToastsStore();

const enableEdit = () => {
    state.form.status = 'editing'
    state.step = 0
    state.latestStep = 3
}
const regenerate = () => {
    state.loading = true;

    completeWizard(state).then((response) => {
        state.setOptimization(response.data.optimization)
        state.form.status = 'complete'
        toast.success('Complete Optimization', 'The optimization was successfully re-generated.')
    })
}
</script>

<template>
    <div class="absolute right-2 top-3" v-show="!state.loading">
        <DropdownMenu v-if="state.form.optimizationId">
            <DropdownMenuTrigger as-child>
                <SidebarMenuButton size="lg" class="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground px-4">
                    <EllipsisVerticalIcon class="ml-auto size-4" />
                </SidebarMenuButton>
            </DropdownMenuTrigger>
            <DropdownMenuContent
                class="w-[--radix-dropdown-menu-trigger-width] min-w-56 rounded-lg"
                side="right"
                align="start"
                :side-offset="4"
            >
                <div class="flex items-stretch flex-col gap-2">
                    <Button text-position="left" v-if="state.form.role.url" target="_blank" as="a" :href="state.form.role.url">
                        <Link /> Apply
                    </Button>
                    <Button text-position="left" :disabled="state.loading" :variant="state.loading ? 'ghost' : 'outline'" type="button" size="lg" @click="regenerate">
                        <Recycle /> Regenerate
                    </Button>
                    <Button text-position="left" :disabled="state.loading" :variant="state.loading ? 'ghost' : 'outline'" type="button" size="lg" @click="enableEdit">
                        <Edit /> Edit
                    </Button>
                    <DeleteOptimization text-position="left" />

                </div>
            </DropdownMenuContent>
        </DropdownMenu>
    </div>
</template>
