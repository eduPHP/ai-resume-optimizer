<script lang="ts" setup>
import { Button } from '@/components/ui/button'
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog'
import { deleteResume } from '@/lib/axios'
import { useOptimizationWizardStore } from '@/stores/OptimizationWizardStore'
import { useToastsStore } from '@/stores/ToastsStore'
import { Trash } from 'lucide-vue-next'

const state = useOptimizationWizardStore()
const toast = useToastsStore()

const props = defineProps<{
    id: number
    size?: 'lg' | 'default' | 'sm' | 'icon'
    textPosition?: 'left' | 'center' | 'right'
    onDelete?: () => void
}>()

const handleDeleteResume = () => {
    deleteResume(state, props.id).then(() => {
        if (props.onDelete) {
            props.onDelete()
        }
    })
    toast.success('Removed!', 'The resume was successfully removed.')
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
            </Button>
        </DialogTrigger>
        <DialogContent>
            <DialogHeader class="space-y-3">
                <DialogTitle>Are you sure you want to delete this resume?</DialogTitle>
                <DialogDescription> Once it is deleted, all of its resources and data will also be permanently deleted! </DialogDescription>
            </DialogHeader>
            <DialogFooter class="gap-2">
                <DialogClose as-child>
                    <Button variant="secondary"> Cancel </Button>
                </DialogClose>

                <Button variant="destructive" :disabled="state.loading">
                    <button type="button" @click.prevent="handleDeleteResume">Delete Resume</button>
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
