<script setup lang="ts">
import { cn } from '@/lib/utils.js'
import type { HTMLAttributes } from 'vue'
const props = defineProps<{
    class?: HTMLAttributes['class'];
}>();
</script>

<template>
    <div :class="cn('w-full h-1 bg-blue-500 rounded overflow-hidden', props.class)">
        <div class="h-1 bg-gradient-to-r from-blue-500 via-purple-300 to-pink-300 animate-slide rounded"></div>
    </div>
</template>

<style>
@keyframes slide {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

.animate-slide {
    animation: slide 3s linear infinite;
}
</style>
