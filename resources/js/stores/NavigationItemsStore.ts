import { defineStore } from 'pinia'
import { type NavGroup, type NavItem } from '@/types';
import { Axios } from '@/lib/axios';
import { format } from 'date-fns';
import { OptimizationType } from '@/stores/OptimizationWizardStore';

type State = {
    items: NavGroup[];
    filter: string;
}

export const useNavigationItemsStore = defineStore('navigation-items', {
    state: (): State => ({
        items: [] as NavGroup[],
        filter: '',
    }),

    getters: {
        navigationItems(state: State): NavGroup[] {
            if (state.filter.trim().length === 0) {
                return state.items
            }

            return state.items.filter((group: NavGroup): boolean => {
                return group.items.some((item: NavItem): boolean => {
                    return item.title.toLowerCase().includes(state.filter.toLowerCase());
                });
            }).map((group: NavGroup): NavGroup => {
                return {
                    ...group,
                    items: group.items.filter((item: NavItem): boolean => {
                        return item.title.toLowerCase().includes(state.filter.toLowerCase());
                    }),
                }
            });
        }
    },

    actions: {
        resetFilter(): void {
            this.filter = '';
        },

        replace(optimization: OptimizationType) {
            const groupIndex: number = this.items.findIndex((group: NavGroup) => group.title === 'Today')
            const itemIndex: number = this.items[groupIndex].items.findIndex((item: NavItem) => item.id === optimization.id)

            this.items[groupIndex].items[itemIndex] = {
                ...this.items[groupIndex].items[itemIndex],
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
                created: format(new Date(), 'yyyy-LL-dd h:mm a'), // Y-m-d g:i A
            };

            let groupIndex: number = this.items.findIndex((group: NavGroup) => group.title === 'Today')

            // handle no 'Today' yet
            if (groupIndex < 0)  {
                this.items.unshift({
                    title: 'Today',
                    items: [],
                })
                groupIndex = 0
            }

            this.items[groupIndex].items.unshift(item)
        },

        async loadItems() {
            const request = await Axios().get<{[key: string]: NavItem[]}>(route('optimizations.index', { grouped: true }));

            this.items = Object.keys(request.data).map(
                (key: string): NavGroup => {
                    const groupItems: NavItem[] = request.data[key];
                    return {
                        title: key,
                        items: groupItems,
                    };
                },
            );
        }

    },
})

export type NavigationItemsStore = ReturnType<typeof useNavigationItemsStore>
