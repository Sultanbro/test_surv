<script>
import DefaultLayout from '@/layouts/DefaultLayout'
import { useAsyncPageData } from '@/composables/asyncPageData'
const KBPage = () => import(/* webpackChunkName: "KBPage" */ '@/pages/KBPage.vue')

export default {
    name: 'KnowledgeBaseView',
    components: {
        DefaultLayout,
        KBPage,
    },
    data(){
        return {
            auth_user_id: 0,
            can_edit: false,
        }
    },
    mounted(){
        useAsyncPageData('/kb').then(data => {
            this.auth_user_id = data.auth_user_id
            this.can_edit = data.can_edit
        }).catch(error => {
            console.error('useAsyncPageData', error)
        })
    }
}
</script>

<template>
    <DefaultLayout v-show="auth_user_id">
        <div class="old__content">
            <KBPage
                :auth_user_id="auth_user_id"
                :can_edit="can_edit"
            />
        </div>
    </DefaultLayout>
</template>

<style>
    .header__profile {
        display: none !important;
    }
    @media (min-width: 1360px) {
        .container.container-left-padding {
            padding-left: 7rem !important;
            padding-right: 6rem !important;
        }
    }
</style>