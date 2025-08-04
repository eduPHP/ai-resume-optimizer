<script setup lang="ts">
import { Head } from '@inertiajs/vue3'

import { type BreadcrumbItem } from '@/types'

import AIInstructions from '@/components/AIInstructions.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import SettingsLayout from '@/layouts/settings/Layout.vue'

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'AI Instructions',
        href: '/settings/instructions',
    },
]
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="AI Instructions Settings" />

        <SettingsLayout>
            <div class="space-y-6">
                <AIInstructions />
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
