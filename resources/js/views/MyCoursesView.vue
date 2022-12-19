<script>
import DefaultLayout from '@/layouts/DefaultLayout'
import { useAsyncPageData } from '@/composables/asyncPageData'
const MyCourse = () => import(/* webpackChunkName: "MyCoursePage" */ '@/pages/MyCourse')

export default {
    name: 'MyCoursesView',
    components: {
        DefaultLayout,
        MyCourse,
    },
    data(){
        return {
            user_id: 0,
        }
    },
    mounted(){
        useAsyncPageData('/my-courses').then(data => {
            this.user_id = data.user_id
        }).catch(error => {
            console.error('useAsyncPageData', error)
        })
    }
}
</script>

<template>
    <DefaultLayout>
        <!-- <script src="/video_learning/playerjs.js" ></script> -->
        <div class="old__content">
            <MyCourse
                v-show="user_id"
                :user_id="user_id"
            />
        </div>
    </DefaultLayout>
</template>

<style>
.header__profile {
    display:none !important;
}
@media (min-width: 1360px) {
.container.container-left-padding {
    padding-left: 7rem !important;
}
}
</style>