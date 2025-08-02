<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    useSidebar
} from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import GithubIcon from '@/components/icons/Github.vue';
import AppLogo from './AppLogo.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Plus, X } from 'lucide-vue-next';
import { useNavigationItemsStore } from '@/stores/NavigationItemsStore';

const nav = useNavigationItemsStore();
const { state: sidebarState } = useSidebar();
const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/eduPHP/ai-resume-optimizer',
        icon: GithubIcon,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>

            </SidebarMenu>

        </SidebarHeader>
        <div class="bg-sidebar pb-2">
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
        </div>
        <SidebarContent>
            <NavMain />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
