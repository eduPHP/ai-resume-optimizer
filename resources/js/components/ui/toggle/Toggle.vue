<script setup lang="ts">
import { defineModel } from 'vue'
import { Switch, SwitchGroup, SwitchLabel } from '@headlessui/vue'

const model = defineModel<boolean | undefined>()

</script>

<template>
    <SwitchGroup as="div" class="flex items-center">
        <Switch v-model="model" :class="[model ? 'bg-gray-400 dark:bg-white/60' : 'bg-gray-200 dark:bg-gray-200', 'relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:ring-1 focus:ring-gray-400 dark:focus:ring-white/60 focus:ring-offset-1 focus:outline-hidden']">
            <span aria-hidden="true" :class="[model ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block size-5 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out']" />
        </Switch>
        <SwitchLabel as="span" class="ml-3 cursor-pointer select-none">
            <span class="dark:text-white">
                <slot />
            </span>
        </SwitchLabel>
    </SwitchGroup>
</template>
