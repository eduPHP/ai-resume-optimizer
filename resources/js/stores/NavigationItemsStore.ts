import { defineStore } from 'pinia'
import { type NavGroup, type NavItem } from '@/types';
import { Axios } from '@/lib/axios';
import { Page } from '@inertiajs/core';

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

        addItem(title: string, id: string) {
            const item: NavItem = {
                href: route('optimizations.show', id),
                id,
                title,
                created: 'Just Now'
            };

            const groupIndex: number = this.items.findIndex((group: NavGroup) => group.title === 'Today')

            console.log(groupIndex)
            // no 'Today' yet
            if (groupIndex < 0)  {
                this.items.unshift({
                    title: 'Today',
                    items: [item]
                })
            } else {
                this.items[groupIndex].items.unshift(item)
            }
        },

        async loadItems(page: Page) {
            const request = await Axios(page).get<{[key: string]: NavItem[]}>(route('optimizations.index', { grouped: true }));

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
