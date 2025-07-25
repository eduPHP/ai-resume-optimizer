<script setup lang="ts">
import { cn } from '@/lib/utils';
import { useVModel } from '@vueuse/core';
import { HTMLAttributes, nextTick, onMounted, ref } from 'vue';

const props = defineProps<{
    autoGrow?: boolean,
    defaultValue?: string | number;
    modelValue?: string | number;
    class?: HTMLAttributes['class'];
    rows?: HTMLAttributes['rows'];
}>();
const textarea = ref<HTMLTextAreaElement | null>(null);

const emits = defineEmits<{
    (e: 'update:modelValue', payload: string | number): void;
}>();

const modelValue = useVModel(props, 'modelValue', emits, {
    passive: true,
    defaultValue: props.defaultValue,
});
onMounted(() => {
    nextTick(() => {
        handleAutoGrow()
    })
})


const handleAutoGrow = () => {
    if (! props.autoGrow) return

    const el = textarea.value as HTMLTextAreaElement

    if (el && el.style) {
        el.style.height = 'auto'
        el.style.height = (el.scrollHeight + 5) + 'px'
    }
}
</script>

<template>
    <textarea
        v-model="modelValue"
        ref="textarea"
        :class="
            cn(
                'flex w-full rounded-md border border-input dark:bg-[#363636] px-3 py-2 text-base ring-offset-[#363636] file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring focus-visible:ring-offset-1 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm',
                props.class,
            )
        "
        :rows="props.rows || 3"
        @input="handleAutoGrow"
    />
</template>
