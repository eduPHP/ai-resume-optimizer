<script setup lang="ts">
import { SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem, useSidebar } from '@/components/ui/sidebar';
import { Link, usePage } from '@inertiajs/vue3';
import { File, Plus, X } from 'lucide-vue-next';
import { onMounted } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { useNavigationItemsStore } from '@/stores/NavigationItemsStore';
import { SharedData } from '@/types';

const nav = useNavigationItemsStore();
const { state: sidebarState } = useSidebar();

const compatibilityStyle = (score: number | undefined) => {
    const page = usePage<SharedData>();

    const {high, medium, low} = page.props.auth?.user.ai_settings.compatibilityScoreLevels
    const SCORE_STYLES = {
        HIGH: 'text-green-400',
        MEDIUM: 'text-yellow-400',
        LOW: 'text-red-400'
    } as const;

    if (! score) {
        return '';
    }

    if (score >= high) return SCORE_STYLES.HIGH;
    if (score >= medium) return SCORE_STYLES.MEDIUM;
    if (score <= low) return SCORE_STYLES.LOW;

    return '';
};


onMounted(() => {
    nav.loadItems()
});
</script>

<template>
    <div :class="{'mx-2': sidebarState !== 'collapsed'}">
        <Button size="lg" as="a" class="w-full mt-3" :class="{'!px-0': sidebarState === 'collapsed', 'bg-gray-200 dark:bg-gray-800': route().current('optimizations.create')}" :href="route('optimizations.create')" :variant="sidebarState === 'collapsed' ? 'ghost' : 'outline'">
            <span v-if="sidebarState === 'collapsed'">
                <Plus class="z-0" />
                <span class="sr-only">New Optimization</span>
            </span>
            <span v-else>New Optimization</span>
        </Button>
        <div class="relative">
            <Input v-if="sidebarState !== 'collapsed'" class="mt-3 h-10" placeholder="Search" v-model="nav.filter" />
            <Button v-if="nav.filter.length" @click.prevent="nav.resetFilter()" variant="ghost" class="absolute inset-0 top-0 bottom-0 left-auto px-2 m-1">
                <X :size="16" class="text-red-400" />
            </Button>
        </div>
        <div class="mt-2 text-sm text-gray-700 dark:text-gray-400 " v-if="nav.filter.length && ! nav.navigationItems.length">
            No entries fround with the keyword <span class="">'{{ nav.filter }}'</span>
        </div>
        <SidebarGroup v-for="group in nav.navigationItems" :key="group.title" class="mb-2 p-0 mt-3">
            <SidebarGroupLabel v-if="sidebarState !== 'collapsed'" class="mt-3">{{ group.title }}</SidebarGroupLabel>
            <SidebarMenu>
                <SidebarMenuItem v-for="item in group.items" :key="item.id">
                    <SidebarMenuButton as-child :is-active="route().routeParams?.optimization === item.id" :tooltip="item.tooltip">
                        <Link :href="item.href" class="mb-2 p-2">
                            <File class="!h-7 !w-7" :class="compatibilityStyle(item.score)" />
                            <span class="flex flex-col">
                                <span>{{ item.title }}</span>
                                <span class="text-xs text-gray-400 dark:text-white/40">Sent at {{ item.created }}</span>
                            </span>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarGroup>
    </div>
</template>
