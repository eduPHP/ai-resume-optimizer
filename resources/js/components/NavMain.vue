<script setup lang="ts">
import { SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem, useSidebar } from '@/components/ui/sidebar'
import { compatibilityStyle, useNavigationItemsStore } from '@/stores/NavigationItemsStore'
import { Link } from '@inertiajs/vue3'
import { useIntersectionObserver } from '@vueuse/core'
import { File } from 'lucide-vue-next'
import { onMounted, ref } from 'vue'

const nav = useNavigationItemsStore()
const { state: sidebarState } = useSidebar()
const loadMoreTrigger = ref(null)

useIntersectionObserver(loadMoreTrigger, ([{ isIntersecting }]) => {
    if (isIntersecting) {
        nav.loadItems()
    }
})

onMounted(() => {
    nav.loadItems()
})
</script>

<template>
    <div :class="{ 'mx-2': sidebarState !== 'collapsed' }">
        <div class="pt-32 text-sm text-gray-700 dark:text-gray-400" v-if="nav.filter.length && !nav.navigationItems.length">
            No entries fround with the keyword <span class="">'{{ nav.filter }}'</span>
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
                            <span class="flex flex-col">
                                <span>{{ item.title }}</span>
                                <span class="text-xs text-gray-400 dark:text-white/40">Sent at {{ item.created }}</span>
                            </span>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarGroup>
        <div ref="loadMoreTrigger" class="h-4"></div>
    </div>
</template>
