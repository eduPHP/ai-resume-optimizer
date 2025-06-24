<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

import HeadingSmall from '@/components/HeadingSmall.vue';
import { type BreadcrumbItem } from '@/types';

import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import AIInstructions from '@/components/AIInstructions.vue';

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'AI Instructions',
        href: '/settings/instructions',
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="AI Instructions Settings" />

        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall title="AI Instructions" description="Add custom instructions for optimizations." />
                <AIInstructions />
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
