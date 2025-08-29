<script setup lang="ts">
import { SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem, useSidebar } from '@/components/ui/sidebar'
import { compatibilityStyle, useNavigationItemsStore } from '@/stores/NavigationItemsStore'
import { Link } from '@inertiajs/vue3'
import { useIntersectionObserver } from '@vueuse/core'
import { File, Check } from 'lucide-vue-next'
import { ref, onMounted } from 'vue'

const nav = useNavigationItemsStore()
const { state: sidebarState } = useSidebar()
const loadMoreTrigger = ref(null)
const debug = ref<boolean>(false)

onMounted(() => {
    nav.init()
})

useIntersectionObserver(
    loadMoreTrigger,
    ([{ isIntersecting }]) => {
        // console.log('Intersection observer triggered:', isIntersecting, 'hasMore:', nav.hasMore, 'loading:', nav.loading)
        if (isIntersecting && nav.hasMore && !nav.loading) {
            // console.log('Calling loadItems from intersection observer')
            nav.loadItems(false)
        }
    },
    {
        threshold: 0.1,
        rootMargin: '50px'
    }
)
</script>

<template>
    <div :class="{ 'mx-2': sidebarState !== 'collapsed' }">
        <div v-if="nav.loading && !nav.navigationItems.length" class="pt-32 pl-2 space-y-4">
        <!-- Repeat for each placeholder row -->
            <div v-for="i in 10" v-bind:key="i" class="flex items-center space-x-3 animate-pulse">
                <div class="w-6 h-6 rounded bg-gray-300 dark:bg-gray-600"></div>
                <div class="flex-1">
                    <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-40 mb-1"></div>
                    <div class="h-3 bg-gray-200 dark:bg-gray-500 rounded w-28"></div>
                </div>
            </div>
        </div>

        <div class="pt-32 text-sm text-gray-700 dark:text-gray-400" v-if="nav.filter.length && !nav.navigationItems.length">
            No entries found with the keyword <span class="">'{{ nav.filter }}'</span>
        </div>
        <SidebarGroup v-for="group in nav.navigationItems" :key="group.title" class="mb-2 mt-3 p-0">
            <SidebarGroupLabel v-if="sidebarState !== 'collapsed'" class="mt-3">{{ group.title }}</SidebarGroupLabel>
            <SidebarMenu>
                <SidebarMenuItem v-for="item in group.items" :key="item.id">
                    <SidebarMenuButton as-child :is-active="route().routeParams?.optimization === item.id" :tooltip="item.tooltip">
                        <Link :href="item.href" class="mb-2 p-2">
                            <div class="relative" :class="compatibilityStyle(item.score)">
                                <File class="!h-8 !w-8" />
                                <span v-if="item.score" class="absolute left-2 top-2 text-[0.6rem]">{{ item.score }}%</span>
                            </div>
                            <span class="flex flex-col flex-1">
                                <div class="flex items-center gap-px">
                                    <Check v-if="item.applied" class="h-4 w-4 text-green-500 flex-shrink-0" />
                                    <span :class="item.applied && 'text-green-500'">{{ item.title }}</span>
                                </div>
                                <span class="text-xs text-gray-400 dark:text-white/40">Sent at {{ item.created }}</span>
                            </span>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarGroup>

        <!-- Debug info and load more trigger -->
        <div v-if="debug" class="p-2 text-xs text-gray-500 border-t">
            <div>Page: {{ nav.page }}</div>
            <div>Has More: {{ nav.hasMore }}</div>
            <div>Loading: {{ nav.loading }}</div>
            <div>Items: {{ nav.items.length }}</div>
        </div>

        <div ref="loadMoreTrigger">
            <div v-if="debug" class="h-20 flex items-center justify-center bg-red-100 text-xs">Load More Trigger</div>
        </div>
    </div>
</template>
