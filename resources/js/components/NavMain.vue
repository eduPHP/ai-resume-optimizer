<script setup lang="ts">
import { SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem, useSidebar } from '@/components/ui/sidebar';
import { type NavGroup, type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { File, Plus, X } from 'lucide-vue-next';
import { onMounted, ref, watch } from 'vue';
import { Axios } from '@/lib/axios';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';

const original = ref<NavGroup[]>([]);
const items = ref<NavGroup[]>([]);
const page = usePage();
const { state: sidebarState } = useSidebar();

const filter = ref('');

watch(filter, (value: string): void => {
    if (value.trim().length === 0) {
        items.value = original.value;

        return;
    }

    items.value = original.value.filter((group: NavGroup): boolean => {
        return group.items.some((item: NavItem): boolean => {
            return item.title.toLowerCase().includes(value.toLowerCase());
        });
    }).map((group: NavGroup): NavGroup => {
        return {
            ...group,
            items: group.items.filter((item: NavItem): boolean => {
                return item.title.toLowerCase().includes(value.toLowerCase());
            }),
        }
    });
})

onMounted(async () => {
    const request = await Axios(page).get<NavItem[]>(route('optimizations.index', { grouped: true }));


    original.value = Object.keys(request.data).map(
        (key: string): NavGroup => ({
            title: key,
            items: request.data[`${key}`] as NavItem[],
        }),
    );
    items.value = original.value;
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
            <Input v-if="sidebarState !== 'collapsed'" class="mt-3 h-10" placeholder="Search" v-model="filter" />
            <Button v-if="filter.length" @click.prevent="filter = ''" variant="ghost" class="absolute inset-0 top-0 bottom-0 left-auto px-2 m-1">
                <X :size="16" class="text-red-400" />
            </Button>
        </div>
        <div class="mt-2 text-sm text-gray-700 dark:text-gray-400 " v-if="filter.length && ! items.length">
            No entries fround with the keyword <span class="">'{{ filter }}'</span>
        </div>
        <SidebarGroup v-for="group in items" :key="group.title" class="mb-2 p-0 mt-3">
            <SidebarGroupLabel v-if="sidebarState !== 'collapsed'" class="mt-3">{{ group.title }}</SidebarGroupLabel>
            <SidebarMenu>
                <SidebarMenuItem v-for="item in group.items" :key="item.id">
                    <SidebarMenuButton as-child :is-active="route().routeParams?.optimization === item.id" :tooltip="item.tooltip">
                        <Link :href="item.href" class="mb-2 p-2">
                            <File class="!h-7 !w-7" />
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
