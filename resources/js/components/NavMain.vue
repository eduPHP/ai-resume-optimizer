<script setup lang="ts">
import { SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type SharedData, type NavGroup, type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { File } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';
import axios from 'axios';

const items = ref<NavGroup[]>([])

onMounted(async () => {
    axios.defaults.headers.common['Authorization'] = `Bearer ${page.props.auth.user.api_token}`
    axios.defaults.headers.common['Accept'] = `application/json`
    axios.defaults.headers.common['Content-Type'] = `application/json`

    const request = await axios.get<NavItem[]>('/api/resumes?grouped=true')

    items.value = Object.keys(request.data).map((key: string): NavGroup => ({
        title: key,
        items: request.data[`${key}`] as NavItem[],
    }));
})

const page = usePage<SharedData>();
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
    <SidebarGroup v-for="group in items" :key="group.title" class="px-2 py-0 mb-2">
        <SidebarGroupLabel>{{ group.title }}</SidebarGroupLabel>
        <SidebarMenu>
            <SidebarMenuItem v-for="item in group.items" :key="item.title">
                <SidebarMenuButton
                    as-child :is-active="item.href === page.url"
                    :tooltip="item.title"
                >
                    <Link :href="item.href" class="mb-4 py-2">
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
