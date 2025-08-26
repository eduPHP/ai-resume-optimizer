<script lang="ts" setup>
import { Button } from '@/components/ui/button'
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog'
import { deleteOptimization } from '@/lib/axios'
import { OptimizationType, useOptimizationWizardStore } from '@/stores/OptimizationWizardStore'
import { useToastsStore } from '@/stores/ToastsStore'
import { router } from '@inertiajs/vue3'
import { Trash } from 'lucide-vue-next'
import { useNavigationItemsStore } from '@/stores/NavigationItemsStore'

const state = useOptimizationWizardStore()
const toast = useToastsStore()
const nav = useNavigationItemsStore()

defineProps<{
    size?: 'lg' | 'default' | 'sm' | 'icon'
    textPosition?: 'left' | 'center' | 'right'
}>()

const handleDeleteOptimization = () => {
    deleteOptimization()
    nav.delete(state.form.optimizationId as string)
    state.setOptimization({} as OptimizationType)

    toast.success('Removed!', 'The optimization was successfully removed.')

    router.visit('/dashboard', { method: 'get', preserveState: true })
}
</script>

<template>
    <Dialog>
        <DialogTrigger as-child>
            <Button
                :disabled="state.loading"
                variant="link"
                class="text-red-400"
                type="button"
                :text-position="textPosition ?? 'center'"
                :size="size ?? 'default'"
            >
                <Trash />
                Remove{{ state.form.status === 'draft' ? ' Draft' : '' }}
            </Button>
        </DialogTrigger>
        <DialogContent>
            <DialogHeader class="space-y-3">
                <DialogTitle>Are you sure you want to delete this optimization?</DialogTitle>
                <DialogDescription> Once it is deleted, all of its resources and data will also be permanently deleted! </DialogDescription>
            </DialogHeader>
            <DialogFooter class="gap-2">
                <DialogClose as-child>
                    <Button variant="secondary"> Cancel </Button>
                </DialogClose>

                <Button variant="destructive" :disabled="state.loading">
                    <button type="button" @click.prevent="handleDeleteOptimization">Delete Optimization</button>
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
