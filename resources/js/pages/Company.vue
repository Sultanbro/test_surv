<template>
    <div class="default-tab">
        <nav class="normal mt-4">
            <ul
                    id="nav-tab"
                    class="nav nav-tabs set-tabs"
                    role="tablist"
            >
                <template v-for="tab in tabs">
                    <li
                            :key="tab.htmlId"
                            :id="`${tab.htmlId}-tab`"
                            class="nav-item"
                    >
                        <span
                                class="nav-link"
                                :class="{active: tab.id === activeTab}"
                                @click="activeTab = tab.id"
                        >{{ tab.title }}
                        </span>
                    </li>
                </template>
            </ul>
        </nav>
        <div
                id="nav-tabContent"
                class="tab-content"
        >
            <div
                    v-if="activeTab === 2 && can(['positions_view', 'settings_view'])"
                    class="tab-pane fade show active py-3"
                    id="nav-home"
                    role="tabpanel"
                    aria-labelledby="nav-home-tab"
            >
                <Professions :positions="pageData.positions"/>
            </div>
            <div
                    v-if="activeTab === 3 && can(['groups_view', 'settings_view'])"
                    id="nav-profile"
                    class="tab-pane fade show active py-3"
                    role="tabpanel"
                    aria-labelledby="nav-profile-tab"
            >
                <Groups
                        :statuseses="pageData.statuseses"
                        :archived_groupss="pageData.archived_groupss"
                        :book_groups="pageData.book_groups"
                        :corpbooks="pageData.corpbooks"
                        :activeuserid="pageData.activeuserid"
                />
            </div>
            <div
                    v-if="activeTab === 4 && can(['settings_view'])"
                    id="nav-shift"
                    class="tab-pane fade show active py-3"
                    role="tabpanel"
                    aria-labelledby="nav-profile-tab"
            >
                <Shifts/>
            </div>
        </div>
    </div>
</template>

<script>
    const Professions = () => import(/* webpackChunkName: "ProfessionsPage" */ '@/pages/professions.vue')
    const Groups = () => import(/* webpackChunkName: "GroupsPage" */ '@/pages/groups.vue')
    const Shifts = () => import(/* webpackChunkName: "GroupsPage" */ '@/pages/shifts.vue')
    import {useAsyncPageData} from '@/composables/asyncPageData'

    export default {
        name: 'Company',
        components: {
            Professions,
            Groups,
            Shifts
        },
        data() {
            return {
                tabs: [
                    {
                        id: 2,
                        htmlId: 'nav-home',
                        path: '/timetracking/settings?tab=2#nav-home',
                        title: 'Должности',
                        access: ['positions_view', 'settings_view']
                    },
                    {
                        id: 3,
                        htmlId: 'nav-profile',
                        path: '/timetracking/settings?tab=3#nav-profile',
                        title: 'Отделы',
                        access: ['groups_view', 'settings_view']
                    },
                    {
                        id: 4,
                        htmlId: 'nav-shift',
                        path: '???',
                        title: 'Смены',
                        access: ['settings_view']
                    },
                ],
                activeTab: 2,
                pageData: {}
            }
        },
        computed: {
            activeTabItem() {
                return this.tabs.find(item => item.id === this.activeTab)
            }
        },
        watch: {
            activeTab(value) {
                this.updatePageData()
            }
        },
        mounted() {
            this.updatePageData()
        },
        methods: {
            can(access) {
                if (access === 'is_admin') return this.$laravel.is_admin
                if (typeof access === 'string') return this.$can(access)
                return access.some(item => this.$can(item))
            },
            updatePageData() {
                this.pageData = {}
                useAsyncPageData(`/timetracking/settings?tab=${this.activeTab}#${this.activeTabItem.htmlId}`).then(data => {
                    this.pageData = data
                }).catch(error => {
                    console.error('useAsyncPageData', error)
                })
            }
        },
    }
</script>