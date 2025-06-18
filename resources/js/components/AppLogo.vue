<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { useSidebar } from '@/components/ui/sidebar';

const { state } = useSidebar();


</script>

<template>
    <div :class="{'size-12': state !== 'collapsed', 'size-8': state === 'collapsed'}" class="flex aspect-square bg-black dark:bg-gray-200 items-center justify-center rounded-md bg-sidebar-primary text-sidebar-primary-foreground">
        <AppLogoIcon :class="{'size-10': state !== 'collapsed', 'size-7': state === 'collapsed'}" class="fill-current text-white dark:text-black" />
    </div>
    <div class="ml-1 grid flex-1 text-left text-sm">
        <span class="mb-0.5 truncate font-semibold leading-none">Resume Optimizer</span>
    </div>
</template>
