<script setup lang="ts">
import GithubIcon from '@/components/icons/Github.vue'
import NavFooter from '@/components/NavFooter.vue'
import NavMain from '@/components/NavMain.vue'
import NavUser from '@/components/NavUser.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    useSidebar,
} from '@/components/ui/sidebar'
import { useNavigationItemsStore, SCORE_STYLES, ScoreLevel } from '@/stores/NavigationItemsStore'
import { type NavItem } from '@/types'
import { Link } from '@inertiajs/vue3'
import { Plus, X } from 'lucide-vue-next'
import AppLogo from './AppLogo.vue'
import debounce from '@/lib/debounce'
import { watch } from 'vue'
import OptimizationListener from '@/components/ui/OptimizationListener.vue'

const nav = useNavigationItemsStore()
const { state: sidebarState } = useSidebar()
const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/eduPHP/ai-resume-optimizer',
        icon: GithubIcon,
    },
]

type Score = {
    id: ScoreLevel
    title: string
    style: string
}

const scores: Score[] = [
    { id: 'all', title: 'Any', style: '' },
    { id: 'top', title: 'Top', style: SCORE_STYLES.TOP },
    { id: 'high', title: 'High', style: SCORE_STYLES.HIGH },
    { id: 'medium', title: 'Medium', style: SCORE_STYLES.MEDIUM },
    { id: 'low', title: 'Low', style: SCORE_STYLES.LOW },
]

// Create the debounced function outside the watcher
const debouncedFilterSearch = debounce(() => {
    // console.log('Debounced filter search triggered:', nav.filter)
    nav.hasMore = true
    nav.loadItems(true)
}, 300)

watch(
    () => nav.filter,
    () => {
        debouncedFilterSearch()
    },
)
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <OptimizationListener />
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
            <div class="flex justify-center gap-2 px-1 pt-1 text-xs">
                <ul class="flex gap-2">
                    <li
                        v-for="score in scores"
                        :key="score.id"
                    >
                        <button
                            @click.prevent="() => {
                                nav.scoreLevel = score.id;
                                nav.loadItems(true);
                            }"
                            :class="{
                                underline: nav.scoreLevel === score.id,
                                [score.style]: true,
                            }"
                        >{{ score.title }}</button>
                    </li>
                </ul>
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
