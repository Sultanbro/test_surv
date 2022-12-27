<script>
import DefaultLayout from '@/layouts/DefaultLayout'
import { useAsyncPageData } from '@/composables/asyncPageData'
const TableComing = () => import(/* webpackChunkName: "TableComingPage" */ '@/pages/TableComing')

export default {
    name: 'EntertimeView',
    components: {
        DefaultLayout,
        TableComing,
    },
    data(){
        return {
            groups: null,
            years: null,
            activeuserid: '',
            activeTab: 'nav-entertime-tab',
            tabs: [
                {
                    id: 'nav-top-tab',
                    path: '/timetracking/top',
                    title: 'TOП',
                    access: 'top_view'
                },
                {
                    id: 'nav-home-tab',
                    path: '/timetracking/reports',
                    title: 'Табель',
                    access: 'tabel_view'
                },
                {
                    id: 'nav-entertime-tab',
                    path: '/timetracking/reports/enter-report',
                    title: 'Время прихода',
                    access: 'entertime_view'
                },
                {
                    id: 'nav-profilex-tab',
                    path: '/timetracking/analytics',
                    title: 'HR',
                    access: 'hr_view'
                },
                {
                    id: 'nav-profile-tab',
                    path: '/timetracking/an',
                    title: 'Аналитика',
                    access: 'analytics_view'
                },
                {
                    id: 'nav-salary-tab',
                    path: '/timetracking/salaries',
                    title: 'Начисления',
                    access: 'salaries_view'
                },
                {
                    id: 'nav-quality-tab',
                    path: '/timetracking/quality-control',
                    title: 'ОКК',
                    access: 'quality_view'
                },
            ],
        }
    },
    mounted(){
        useAsyncPageData('/timetracking/reports/enter-report').then(data => {
            this.groups = data.groups
            this.years = data.years
            this.activeuserid = '' + (data.activeuserid || '')
        }).catch(error => {
            console.error('useAsyncPageData', error)
        })
    }
}
</script>

<template>
    <DefaultLayout>
        <div class="old__content">
            <div class="row">
                <div class="col-md-12 mt-4 mb-3">
                    <nav>
                        <ul
                            id="nav-tab"
                            class="nav nav-tabs"
                        >
                            <template v-for="tab in tabs">
                                <li
                                    v-if="$can(tab.access)"
                                    :key="tab.id"
                                    class="nav-item"
                                >
                                    <router-link
                                        :to="tab.path"
                                        :id="tab.id"
                                        class="nav-link"
                                        :class="{active: tab === activeTab}"
                                    >{{ tab.title }}</router-link>
                                </li>
                            </template>
                        </ul>
                    </nav>
                </div>
                <div class="col-md-12">
                    <TableComing
                        v-show="activeuserid"
                        :groups="groups"
                        :years="years"
                        :activeuserid="activeuserid"
                    />
                </div>
            </div>
        </div>
    </DefaultLayout>
</template>

<style scoped>
.header__profile {
    display:none !important;
}
@media (min-width: 1360px) {
    .container.container-left-padding {
        padding-left: 7rem !important;
    }
}
</style>