<script lang="ts" setup>
import { Trash } from 'lucide-vue-next';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { OptimizationType, useOptimizationWizardStore } from '@/stores/OptimizationWizardStore';
import { router } from '@inertiajs/vue3';
import { deleteOptimization } from '@/lib/axios';
import { useToastsStore } from '@/stores/ToastsStore';

const state = useOptimizationWizardStore();
const toast = useToastsStore();

defineProps<{
    size?: 'lg' | 'default' | 'sm' | 'icon';
}>();

const handleDeleteOptimization = () => {
    deleteOptimization();
    state.setOptimization({} as OptimizationType);
    toast.success('Removed!', 'The optimization was successfully removed.');

    router.visit('/dashboard', { method: 'get', preserveState: true });
};
</script>

<template>
    <Dialog>
        <DialogTrigger as-child>
            <Button :disabled="state.loading" variant="destructive" type="button" :size="size ?? 'default'">
                <Trash />
                Remove{{ state.form.status === 'draft' ? ' Draft' : '' }}
            </Button>
        </DialogTrigger>
        <DialogContent>
            <DialogHeader class="space-y-3">
                <DialogTitle>Are you sure you want to delete this optimization?</DialogTitle>
                <DialogDescription> Once it is deleted, all of its resources and data will also be permanently deleted. </DialogDescription>
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
