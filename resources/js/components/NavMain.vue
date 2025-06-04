<script setup lang="ts">
import { SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavGroup, type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { File } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';
import { Axios } from '@/lib/axios';

const items = ref<NavGroup[]>([])
const page = usePage();

onMounted(async () => {
    const request = await Axios(page).get<NavItem[]>(route('optimizations.index', {grouped: true}))

    items.value = Object.keys(request.data).map((key: string): NavGroup => ({
        title: key,
        items: request.data[`${key}`] as NavItem[],
    }));
})

</script>

<template>
    <SidebarMenuButton
        as-child :is-active="'/optimizations/create' === page.url"
        :tooltip="'New Optimization'"
    >
        <Link :href="'/optimizations/create'" class="justify-center bg-gray-200 dark:bg-gray-800">
            <span>New Optimization</span>
        </Link>
    </SidebarMenuButton>
    <SidebarGroup v-for="group in items" :key="group.title" class="py-0 mb-2">
        <SidebarGroupLabel>{{ group.title }}</SidebarGroupLabel>
        <SidebarMenu>
            <SidebarMenuItem v-for="item in group.items" :key="item.id">
                <SidebarMenuButton
                    as-child :is-active="item.href === page.url"
                    :tooltip="item.title"
                >
                    <Link :href="item.href" class=" mb-4 py-2 dark:bg-[#262626]">
                        <File class="!w-7 !h-7" />
                        <span class="flex flex-col">
                            <span>{{ item.title }}</span>
                            <span class="text-xs text-gray-400 dark:text-white/40 ">Sent at {{ item.created }}</span>
                        </span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
