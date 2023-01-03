<script>
import DefaultLayout from '@/layouts/DefaultLayout'
import { useAsyncPageData } from '@/composables/asyncPageData'
const ReportsNav = () => import(/* webpackChunkName: "ReportsNav" */ '@/components/layouts/ReportsNav.vue')
const Analytics = () => import(/* webpackChunkName: "HRPage" */ '@/pages/Analytics.vue')

export default {
    name: 'HRView',
    components: {
        DefaultLayout,
        ReportsNav,
        Analytics,
    },
    data(){
        return {
            groups: null,
            activeuserid: '0',
            activeTab: 'nav-profilex-tab',
        }
    },
    mounted(){
        useAsyncPageData('/timetracking/analytics').then(data => {
            this.groups = data.groups || null
            this.activeuserid = '' + data.activeuserid
        }).catch(error => {
            console.error('useAsyncPageData', error)
        })
    }
}
</script>

<template>
    <DefaultLayout class="no-profile">
        <div class="old__content">
            <div class="row">
                <div class="col-md-12 mt-4 mb-3">
                    <ReportsNav :active-tab="activeTab"/>
                    <div class="col-md-12">
                        <Analytics
                            v-show="activeuserid"
                            :groups="groups"
                            :activeuserid="activeuserid"
                        />
                    </div>
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
        padding-left: 9rem !important;
    }
}
</style>