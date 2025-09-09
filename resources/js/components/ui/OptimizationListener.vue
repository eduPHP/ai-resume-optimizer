<template>
    <div>
        <p v-if="message">{{ message }}</p>
    </div>
</template>

<script setup lang="ts">
import { useEcho } from "@laravel/echo-vue";
import { onMounted, onBeforeUnmount, ref } from "vue";
import { usePage} from '@inertiajs/vue3'
import { SharedData } from '@/types'
import { OptimizationType, useOptimizationWizardStore } from '@/stores/OptimizationWizardStore'
import { useNavigationItemsStore } from '@/stores/NavigationItemsStore'
import { Axios } from '@/lib/axios'
import { useToastsStore } from '@/stores/ToastsStore'
import { router } from '@inertiajs/vue3'

const page = usePage<SharedData>()
const message = ref<string | null>(null);
const state = useOptimizationWizardStore()
const nav = useNavigationItemsStore()
const toast = useToastsStore()

type OptimizationComplete = {
    optimization: {
        id: string
    }
}
// Subscribe to the private channel
const { listen, leave } = useEcho<OptimizationComplete>(
    `optimizations.${page.props.auth.user.id}`, // include "private-"
    ".optimization.complete", // match broadcastAs()
    (e: OptimizationComplete) => {
        Axios().get<{optimization: OptimizationType}>(route('api.optimizations.show', e.optimization.id)).then((response) => {
            const optimization = response.data.optimization
            if (state.form.optimizationId === optimization.id) {
                state.setOptimization(optimization)
            }
            nav.replace({
                id: response.data.optimization.id,
                role_company: response.data.optimization.role_company,
                status: response.data.optimization.status,
                applied: response.data.optimization.applied,
                ai_response: response.data.optimization.ai_response,
            } as OptimizationType)
            toast.success('Optimized!', 'The optimization was successfully completed!')
        })
    }
);

onMounted(listen);
onBeforeUnmount(leave);
</script>
