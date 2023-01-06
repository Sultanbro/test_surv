<template>
    <div class="col-12 col-xl-3 col-lg-4 news-birthday-col">
        <div class="news-birthday">
            <div class="news-birthday-header">
                <p class="news-birthday-header__text">Дни рождения</p>
                <img
                    class="news-birthday-header__menu news-icon"
                    src="/icon/news/post-actions/menu.svg"
                    alt="img">
            </div>


            <div :class="'news-birthday-body ' + (nextPage != null ? '' : 'news-birthday-body--without-border')">
                <birthday-user v-for="user in usersBirthday" :key="user.id" :user="user" />
            </div>

            <a v-show="nextPage != null" class="news-birthday-footer__button" @click="getNextPage" v-html="'Загрузить ещё'"></a>
        </div>
    </div>
</template>

<script>
import Vue from 'vue'
Vue.component('birthday-user', require('@/pages/News/BirthdayUser').default);

export default {
    name: 'BirthdayFeed',
    data() {
        return {
            usersBirthday: [],
            nextPage: null,
        }
    },
    mounted() {
        this.getUsersBirthDay()
    },
    methods: {
        async getUsersBirthDay() {
            await axios.get('/birthdays')
                .then(res => {
                    this.usersBirthday = res.data.data.birthdays;
                    this.nextPage = res.data.data.pagination.next_page_url;
                })
                .catch(res => {
                    console.log(res)
                })
        },

        async getNextPage() {
            await axios.get(this.nextPage)
                .then(res => {
                    console.log(res);
                    this.usersBirthday = this.usersBirthday.concat(res.data.data.birthdays);
                    this.nextPage = res.data.data.pagination.next_page_url;
                })
                .catch(res => {
                    console.log(res)
                })
        }
    }
}
</script>
