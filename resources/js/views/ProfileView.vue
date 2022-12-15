<script>
import DefaultLayout from '@/layouts/DefaultLayout'
const ProfilePage = () => import(/* webpackChunkName: "ProfilePage" */ '@/pages/Profile/ProfilePage')

export default {
    name: 'ProfileView',
    components: {
        DefaultLayout,
        ProfilePage,
    }
}
</script>

<template>
    <DefaultLayout>
        <ProfilePage/>
    </DefaultLayout>
</template>