<template>
    <div class="header__profile _anim _anim-no-hide custom-scroll-y" :class="{
        'v-loading': loading,
        hidden: hide,
        '_active': inViewport
    }">
        <div class="profile__content">
            <div class="profile__col">
                <div class="profile__balance">
                    Текущий баланс
                    <p>{{ balance }} <span>{{ currency }}</span></p>
                </div>
            </div>

            <div class="profile__col">
                <profile-info :data="userInfo"/>
            </div>
        </div>
    </div>
</template>

<script>
import { bus } from '../../bus'

export default {
    name: 'ProfileSidebar',
    props: {},
    data: function () {
        return {
            loading: false,
            hide: false,
            inViewport: false,
        };
    },
    computed: {
        balance(){
            return bus.$data.profileSidebar.balance
        },
        currency(){
            return bus.$data.profileSidebar.currency
        },
        userInfo(){
            return bus.$data.profileSidebar.userInfo
        },
    },
    mounted(){
        const scrollObserver = new IntersectionObserver(() => {
            this.inViewport = true
        })
        scrollObserver.observe(this.$el)
    },
    created(){},
    methods: {}
};
</script>

<style lang="scss">
</style>