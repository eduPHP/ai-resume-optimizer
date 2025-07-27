<script setup lang="ts">
import { SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem, useSidebar } from '@/components/ui/sidebar';
import { Link } from '@inertiajs/vue3';
import { File, Plus, X } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';
import { useIntersectionObserver } from '@vueuse/core';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { useNavigationItemsStore, compatibilityStyle } from '@/stores/NavigationItemsStore';

const nav = useNavigationItemsStore();
const { state: sidebarState } = useSidebar();
const loadMoreTrigger = ref(null);

useIntersectionObserver(loadMoreTrigger, ([{ isIntersecting }]) => {
    if (isIntersecting) {
        nav.loadItems();
    }
});

onMounted(() => {
    nav.loadItems();
});
</script>

<template>
    <div :class="{ 'mx-2': sidebarState !== 'collapsed' }">
        <Button
            size="lg"
            as="a"
            class="mt-3 w-full"
            :class="{ '!px-0': sidebarState === 'collapsed', 'bg-gray-200 dark:bg-gray-800': route().current('optimizations.create') }"
            :href="route('optimizations.create')"
            :variant="sidebarState === 'collapsed' ? 'ghost' : 'outline'"
        >
            <span v-if="sidebarState === 'collapsed'">
                <Plus class="z-0" />
                <span class="sr-only">New Optimization</span>
            </span>
            <span v-else>New Optimization</span>
        </Button>
        <div class="relative">
            <Input v-if="sidebarState !== 'collapsed'" class="mt-3 h-10" placeholder="Search" v-model="nav.filter" />
            <Button
                v-if="nav.filter.length"
                @click.prevent="nav.resetFilter()"
                variant="ghost"
                class="absolute inset-0 bottom-0 left-auto top-0 m-1 px-2"
            >
                <X :size="16" class="text-red-400" />
            </Button>
        </div>
        <div class="mt-2 text-sm text-gray-700 dark:text-gray-400" v-if="nav.filter.length && !nav.navigationItems.length">
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
                                <span v-if="item.score" class="absolute top-2 left-2 text-[0.6rem]">{{ item.score }}%</span>
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
