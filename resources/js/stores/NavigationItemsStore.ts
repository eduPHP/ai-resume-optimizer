import { defineStore } from 'pinia'
import { type NavGroup, type NavItem, SharedData } from '@/types';
import { Axios } from '@/lib/axios';
import { format, isToday, isYesterday } from 'date-fns';
import { OptimizationType } from '@/stores/OptimizationWizardStore';
import { usePage } from '@inertiajs/vue3';

type State = {
    items: NavItem[];
    filter: string;
}

const dateGroup = (date: Date): string => {
    if (isToday(date)) {
        return 'Today';
    }
    if (isYesterday(date)) {
        return 'Yesterday'
    }

    return 'Previous Days';
}

const maybeApplyFilter = (items: NavItem[], filter: string) => {
    if (filter.trim().length === 0) {
        return [...items]
    }

    return items.filter((item: NavItem) => item.title.toLowerCase().includes(filter.toLowerCase()))
}

export const SCORE_STYLES = {
    HIGH: 'text-green-400',
    MEDIUM: 'text-yellow-400',
    LOW: 'text-red-400',
} as const;

export const compatibilityStyle = (score: number | undefined) => {
    const page = usePage<SharedData>();

    const { high, medium } = page.props.auth?.user.ai_settings.compatibilityScoreLevels;

    if (!score) {
        return '';
    }

    if (score >= high) return SCORE_STYLES.HIGH;
    if (score >= medium) return SCORE_STYLES.MEDIUM;
    if (score < medium) return SCORE_STYLES.LOW;

    return '';
};

export const useNavigationItemsStore = defineStore('navigation-items', {
    state: (): State => ({
        items: [] as NavItem[],
        filter: '',
    }),

    getters: {
        navigationItems(state: State): NavGroup[] {
            const items: NavItem[] = maybeApplyFilter(state.items, state.filter)

            // initialize groups
            const groupedItems: NavGroup[] = ['Today', 'Yesterday', 'Previous Days'].map((title: string) => ({
                title,
                items: [],
            }));

            items.map((item: NavItem): void => {
                const groupIndex = groupedItems.findIndex((group: NavGroup) => group.title === item.group)

                groupedItems[groupIndex].items.push(item)
            });

            return groupedItems.filter((group: NavGroup) => group.items.length > 0)
        }
    },

    actions: {
        resetFilter(): void {
            this.filter = '';
        },

        replace(optimization: OptimizationType) {
            const itemIndex: number = this.items.findIndex((item: NavItem) => item.id === optimization.id)

            this.items[itemIndex] = {
                ...this.items[itemIndex],
                title: optimization.role_company,
                status: optimization.status,
                score: optimization.ai_response?.compatibility_score,
            }
        },

        addItem(title: string, id: string, draft: boolean = false) {
            const item: NavItem = {
                href: route('optimizations.show', id),
                id,
                title: draft ? `[draft] ${title}` : title,
                group: 'Today',
                created: format(new Date(), 'yyyy-LL-dd h:mm a'), // Y-m-d g:i A
            };

            this.items.unshift(item)
        },

        async loadItems() {
            const request = await Axios().get<NavItem[]>(route('optimizations.index'));

            this.items = request.data.map((item: NavItem): NavItem => {
                const created = new Date(item.created as string)
                return {
                    ...item,
                    group: dateGroup(created),
                    created: format(created, 'yyyy-LL-dd h:mm a'),
                }
            });
        }

    },
})

export type NavigationItemsStore = ReturnType<typeof useNavigationItemsStore>
