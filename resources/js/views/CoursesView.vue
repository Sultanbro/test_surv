<script>
import DefaultLayout from '@/layouts/DefaultLayout'
const Courses = () => import(/* webpackChunkName: "CoursesPage" */ '@/pages/Courses')

export default {
    name: 'CoursesView',
    components: {
        DefaultLayout,
        Courses,
    },
}
</script>

<template>
    <DefaultLayout>
        <div class="old__content">
            <Courses/>
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