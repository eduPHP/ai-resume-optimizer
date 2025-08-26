<script lang="ts" setup>
import { X } from 'lucide-vue-next'
import { Toast, useToastsStore } from '@/stores/ToastsStore'
import { onMounted, onUnmounted, ref, computed } from 'vue'

const toasts = useToastsStore()

const props = defineProps<{
    toast: Toast,
}>()

let timer: number | undefined = undefined
let startTime: number
let pausedTime = 0
const isPaused = ref(false)
const elapsedTime = ref(0)

const duration = computed(() => props.toast.duration ?? 5000)
const progress = computed(() => {
    const remaining = Math.max(0, duration.value - elapsedTime.value)
    return (remaining / duration.value) * 100
})

const startTimer = () => {
    if (isPaused.value) return

    startTime = Date.now() - pausedTime

    const remainingTime = duration.value - pausedTime
    if (remainingTime <= 0) {
        toasts.removeToast(props.toast)
        return
    }

    // Update progress less frequently - every 50ms is sufficient for smooth animation
    const updateProgress = () => {
        if (!isPaused.value) {
            elapsedTime.value = Date.now() - startTime
            if (elapsedTime.value >= duration.value) {
                toasts.removeToast(props.toast)
                return
            }
        }
        requestAnimationFrame(updateProgress)
    }
    requestAnimationFrame(updateProgress)

    timer = setTimeout(() => {
        if (!isPaused.value) {
            toasts.removeToast(props.toast)
        }
    }, remainingTime)
}

const pauseTimer = () => {
    if (!isPaused.value && timer) {
        isPaused.value = true
        pausedTime = Date.now() - startTime
        clearTimeout(timer)
        timer = undefined
    }
}

const resumeTimer = () => {
    if (isPaused.value) {
        isPaused.value = false
        startTimer()
    }
}

const handleVisibilityChange = () => {
    if (document.hidden) {
        pauseTimer()
    } else {
        resumeTimer()
    }
}

onMounted(() => {
    startTimer()
    document.addEventListener('visibilitychange', handleVisibilityChange)
})

onUnmounted(() => {
    if (timer) {
        clearTimeout(timer)
    }
    document.removeEventListener('visibilitychange', handleVisibilityChange)
})
</script>

<template>
    <li>
        <div
            class="block w-full rounded p-4 md:min-w-96"
            :class="{
                'bg-green-600': toast.type === 'success',
                'bg-red-600': toast.type === 'error',
            }"
            @mouseenter="pauseTimer"
            @mouseleave="resumeTimer"
        >
            <button type="button" class="absolute right-0 top-0 mr-5 mt-6" @click="toasts.removeToast(toast)">
                <X :size="16" />
            </button>
            <h4 class="font-bold">{{ toast.title }}</h4>
            <p class="text-sm">
                {{ toast.description }}
            </p>
        </div>
        <div
            class="duration-linear -mt-1 h-1 bg-white/30 transition-all"
            :style="{
                width: `${progress.toFixed(1)}%`,
                animationPlayState: isPaused ? 'paused' : 'running'
            }"
        />
    </li>
</template>
