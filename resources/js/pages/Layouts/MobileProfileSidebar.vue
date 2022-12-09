<template>
    <div class="header__profile _anim _anim-no-hide custom-scroll-y" :class="{
        'v-loading': loading,
        hidden: hide,
        '_active': inViewport
    }">
        <div class="profile__content">
            <div class="profile__col">
                <start-day-btn
                    v-if="showButton"
                    :status="buttonStatus"
                    :workdayStatus="workdayStatus"
                    @clickStart="startDay"
                />
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
    name: 'MobileProfileSidebar',
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
        workdayStatus(){
            return bus.$data.profileSidebar.workdayStatus
        },
        buttonStatus(){
            return bus.$data.profileSidebar.buttonStatus
        },
        showButton(){
            if(this.$viewportSize.width < 768) return false
            if(this.$can('ucalls_view') && !this.$laravel.is_admin) return false
            return this.workdayStatus === 'started' || (this.userInfo.user && this.userInfo.user.user_type === 'remote')
        }
    },
    mounted(){
        const scrollObserver = new IntersectionObserver(() => {
            this.inViewport = true
        })
        scrollObserver.observe(this.$el)
    },
    created(){},
    methods: {
        startDay(){
            bus.$emit('MobileProfileSidebarStartDay')
        }
    }
};
</script>

<style lang="scss">
</style>