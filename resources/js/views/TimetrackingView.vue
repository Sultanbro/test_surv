<script>
import DefaultLayout from '@/layouts/DefaultLayout'
import { useAsyncPageData } from '@/composables/asyncPageData'
const TableReport = () => import(/* webpackChunkName: "TableReportPage" */ '@/pages/TableReport')

export default {
    name: 'QualityControlView',
    components: {
        DefaultLayout,
        TableReport,
    },
    data(){
        return {
            groups: null,
            fines: null,
            years: null,
            can_edit: false,
            activeuserid: '',
            activeuserpos: '',
            activeTab: 'nav-home-tab',
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
        useAsyncPageData('/timetracking/reports').then(data => {
            this.groups = data.groups || null
            this.fines = data.fines || null
            this.years = data.years || null
            this.can_edit = !!data.can_edit
            this.activeuserid = '' + data.activeuserid
            this.activeuserpos = '' + data.activeuserpos
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
                    <TableReport
                        v-show="activeuserid"
                        :groups="groups"
                        :fines="fines"
                        :years="years"
                        :can_edit="can_edit"
                        :activeuserid="activeuserid"
                        :activeuserpos="activeuserpos"
                    />
                </div>
            </div>
        </div>
    </DefaultLayout>
</template>

<style>
.header__profile {
    display:none !important;
}
@media (min-width: 1360px) {
.container.container-left-padding {
    padding-left: 9rem !important;
}
}
</style>