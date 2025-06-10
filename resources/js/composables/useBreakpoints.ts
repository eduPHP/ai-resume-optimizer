import { computed, onMounted, onUnmounted, ref } from "vue"
import tailwindConfig from 'tailwindcss/defaultConfig';

export const useBreakpoints = () => {
    const windowWidth = ref(window?.innerWidth)
    const screens = tailwindConfig.theme?.screens as {[size: string]: string};

    const onWidthChange = () => windowWidth.value = window?.innerWidth
    onMounted(() => window?.addEventListener('resize', onWidthChange))
    onUnmounted(() => window?.removeEventListener('resize', onWidthChange))

    const breakpoint = computed(() => {
        if (windowWidth.value <= parseInt(screens['xs'] ?? '')) return 'xs'
        else if (windowWidth.value <= parseInt(screens['sm'] ?? '')) return 'sm'
        else if (windowWidth.value <= parseInt(screens['md'] ?? '')) return 'md'
        else if (windowWidth.value <= parseInt(screens['lg'] ?? '')) return 'lg'
        else if (windowWidth.value <= parseInt(screens['xl'] ?? '')) return 'xl'
        else return '2xl' // Fires when windowWidth.value >= 1200
    })

    const width = computed(() => windowWidth.value)

    return { width, breakpoint }
}
