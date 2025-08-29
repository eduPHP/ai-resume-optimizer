import { Axios } from '@/lib/axios'
import { OptimizationType } from '@/stores/OptimizationWizardStore'
import { type NavGroup, type NavItem, SharedData } from '@/types'
import { usePage } from '@inertiajs/vue3'
import { format, isToday, isYesterday } from 'date-fns'
import { defineStore } from 'pinia'

export type ScoreLevel = 'top' | 'high' | 'medium' | 'low' | 'all'

type State = {
    items: NavItem[]
    filter: string
    scoreLevel: ScoreLevel
    page: number
    hasMore: boolean
    loading: boolean
}

// BroadcastChannel for syncing between tabs
const broadcastChannel = new BroadcastChannel('optimization-updates')

const normalizeLevels = (levels: { top: number; high: number; medium: number; low: number }) => {
    const low = Math.max(0, Math.min(100, Math.floor(levels.low)))
    const mediumBase = Math.max(low, Math.min(100, Math.floor(levels.medium)))
    const highBase = Math.max(mediumBase, Math.min(100, Math.floor(levels.high)))
    const top = Math.max(highBase, Math.min(100, Math.floor(levels.top)))
    const medium = mediumBase
    const high = highBase
    return { top, high, medium, low }
}

const dateGroup = (date: Date): string => {
    if (isToday(date)) {
        return 'Today'
    }
    if (isYesterday(date)) {
        return 'Yesterday'
    }

    return 'Previous Days'
}

const maybeApplyFilter = (items: NavItem[], filter: string, scoreLevel: ScoreLevel) => {
    let filteredItems = [...items]

    // Apply text filter
    if (filter.trim().length > 0) {
        filteredItems = filteredItems.filter((item: NavItem) =>
            item.title.toLowerCase().includes(filter.toLowerCase())
        )
    }

    // Apply score level filter
    if (scoreLevel !== 'all') {
        const page = usePage<SharedData>()
        const raw = page.props.auth?.user.ai_settings.compatibilityScoreLevels
        const { top, high, medium, low } = normalizeLevels(raw)

        filteredItems = filteredItems.filter((item: NavItem) => {
            if (!item.score) return false

            switch (scoreLevel) {
                case 'top':
                    return item.score >= top
                case 'high':
                    return item.score >= high
                case 'medium':
                    return item.score >= medium && item.score < high
                case 'low':
                    return item.score >= low && item.score < medium
                default:
                    return true
            }
        })
    }

    return filteredItems
}

export const SCORE_STYLES = {
    HIGH: 'text-green-400',
    MEDIUM: 'text-yellow-400',
    LOW: 'text-red-400',
    TOP: 'text-blue-300',
} as const

export const compatibilityStyle = (score: number | undefined) => {
    const page = usePage<SharedData>()

    const { top, high, medium } = page.props.auth?.user.ai_settings.compatibilityScoreLevels

    if (!score) {
        return ''
    }

    if (score >= top) return SCORE_STYLES.TOP
    if (score >= high) return SCORE_STYLES.HIGH
    if (score >= medium) return SCORE_STYLES.MEDIUM
    if (score < medium) return SCORE_STYLES.LOW

    return ''
}

export const useNavigationItemsStore = defineStore('navigation-items', {
    state: (): State => ({
        items: [] as NavItem[],
        filter: '',
        scoreLevel: 'all',
        page: 1,
        hasMore: true,
        loading: true,
    }),

    getters: {
        navigationItems(state: State): NavGroup[] {
            const items: NavItem[] = maybeApplyFilter(state.items, state.filter, state.scoreLevel)

            // initialize groups
            const groupedItems: NavGroup[] = ['Today', 'Yesterday', 'Previous Days'].map((title: string) => ({
                title,
                items: [],
            }))

            items.map((item: NavItem): void => {
                const groupIndex = groupedItems.findIndex((group: NavGroup) => group.title === item.group)

                groupedItems[groupIndex].items.push(item)
            })

            return groupedItems.filter((group: NavGroup) => group.items.length > 0)
        },
    },

    actions: {
        async resetFilter(): Promise<void> {
            this.filter = ''
            this.page = 1
            await this.loadItems(this.scoreLevel === 'all')
        },

        async resetFilters(): Promise<void> {
            this.filter = ''
            this.scoreLevel = 'all'
            await this.loadItems(true)
        },

        // Initialize broadcast listener when store is created
        init() {
            this.initializeBroadcastListener()
            this.loadItems(true)
        },

        replace(optimization: OptimizationType) {
            const itemIndex: number = this.items.findIndex((item: NavItem) => item.id === optimization.id)

            // Broadcast the update to other tabs
            if (itemIndex === -1) {
                return
            }

            this.items[itemIndex] = {
                ...this.items[itemIndex],
                title: optimization.role_company,
                status: optimization.status,
                applied: optimization.applied,
                score: optimization.ai_response?.compatibility_score,
            } as NavItem

            this.broadcastChanges(optimization, 'optimization-updated')
        },

        delete(optimizationId: string) {
            const itemIndex: number = this.items.findIndex((item: NavItem) => item.id === optimizationId)
            if (itemIndex === -1) {
                return
            }

            this.items.splice(itemIndex, 1)
            broadcastChannel.postMessage({
                type: 'optimization-deleted',
                optimizationId: optimizationId,
            })
        },

        broadcastChanges(optimization: OptimizationType, type: string) {
            broadcastChannel.postMessage({
                type,
                optimizationId: optimization.id,
                updates: {
                    title: optimization.role_company,
                    status: optimization.status,
                    applied: optimization.applied,
                    score: optimization.ai_response?.compatibility_score,
                }
            })
        },

        // Listen for updates from other tabs
        initializeBroadcastListener() {
            broadcastChannel.onmessage = (event) => {
                if (event.data.type === 'optimization-updated') {
                    const { optimizationId, updates } = event.data
                    const itemIndex = this.items.findIndex((item: NavItem) => item.id === optimizationId)

                    if (itemIndex === -1) {
                        return
                    }

                    this.items[itemIndex] = {
                        ...this.items[itemIndex],
                        ...updates
                    }
                }

                if (event.data.type === 'optimization-deleted') {
                    const { optimizationId } = event.data
                    const itemIndex = this.items.findIndex((item: NavItem) => item.id === optimizationId)
                    if (itemIndex === -1) {
                        return
                    }
                    this.items.splice(itemIndex, 1)
                }

                if (event.data.type === 'optimization-created') {
                    const { optimizationId, updates } = event.data
                    const item: NavItem = {
                        href: route('optimizations.show', optimizationId),
                        id: optimizationId,
                        group: 'Today',
                        created: format(new Date(), 'yyyy-LL-dd h:mm a'), // Y-m-d g:i A
                        ...updates,
                    }

                    this.items.unshift(item)
                }
            }
        },

        addItem(title: string, id: string, draft: boolean = false) {
            const item: NavItem = {
                href: route('optimizations.show', id),
                id,
                title: draft ? `[draft] ${title}` : title,
                group: 'Today',
                created: format(new Date(), 'yyyy-LL-dd h:mm a'), // Y-m-d g:i A
            }

            this.items.unshift(item)
            this.broadcastChanges({
                role_company: item.title,
                id,
                status: draft ? 'draft' : 'pending',
                applied: false,
            } as OptimizationType, 'optimization-created')

        },

        async loadItems(reset: boolean = false) {
            console.log('loadItems called with reset:', reset, 'page:', this.page, 'hasMore:', this.hasMore, 'loading:', this.loading)

            if (!this.hasMore) return

            if (reset) {
                console.log('Resetting items and page')
                this.items = []
                this.page = 1
                this.hasMore = true
            }

            this.loading = true

            console.log('Making request for page:', this.page, 'filter:', this.filter, 'scoreLevel:', this.scoreLevel)

            const request = await Axios().get(
                route('optimizations.index', {
                    page: this.page,
                    q: this.filter,
                    score: this.scoreLevel,
                }),
            )

            const data = request.data.data as NavItem[]
            const next = request.data.next_page_url

            console.log('Received data:', data.length, 'items, next_page_url:', next)

            const mapped = data.map((item: NavItem): NavItem => {
                const created = new Date(item.created as string)
                return {
                    ...item,
                    group: dateGroup(created),
                    created: format(created, 'yyyy-LL-dd h:mm a'),
                }
            })

            if (reset) {
                this.items = mapped
            } else {
                this.items.push(...mapped)
            }

            console.log('Total items after update:', this.items.length)

            this.page += 1
            this.hasMore = Boolean(next)
            this.loading = false

            console.log('Updated page to:', this.page, 'hasMore:', this.hasMore)
        },
    },
})

export type NavigationItemsStore = ReturnType<typeof useNavigationItemsStore>
