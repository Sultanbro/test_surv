<template>
    <div class="news-birthday-card">
        <img
            class="news-birthday-card__image"
            :src="user.avatar"
            alt="img">
        <div class="news-birthday-card__body">
            <span class="news-birthday-card__name" v-html="user.name"/>
            <span :class="'news-birthday-card__birthday ' + getCardColor(user)"
                  v-html="user.date_human"/>
        </div>
        <div
            :class="'news-birthday-card__gift ' +(success ? 'news-birthday-card__gift--success' : (hover ? 'news-birthday-card__gift--hover' : ''))"
            @click="togleShowModal(true)"
            @mouseleave="hover=false"
            @mouseenter="hover=true">
            <img v-show="!success" alt="" :src="hover ? '/icon/news/birthday/money-hover.svg' : '/icon/news/birthday/money.svg'">
            <img alt="" v-show="success" src="/icon/news/birthday/arrow.svg">
        </div>

        <div v-show="hover" class="news-money-title">
            <img alt="" class="news-money-title__img" src="/icon/news/birthday/money-title.svg">
            <span class="news-money-title__text" v-html="'Подарить деньги'"/>
        </div>

        <div v-show="showModal" class="news-gift-popup">
            <div class="news-gift-popup__container">

                <img class="news-gift-popup__close" @click="togleShowModal(false)" alt="" src="/icon/news/birthday/close.svg">

                <div class="news-gift-popup__header">
                    <img alt="" class="news-gift-popup__img" src="/icon/news/birthday/money-title.svg">
                    <span class="news-gift-popup__text" v-html="'Подарить деньги'"/>
                </div>

                <div class="news-gift-popup__body">
                    <input class="news-gift-popup__input" type="number" placeholder="Укажите сумму" v-model="summ">
                    <a class="news-gift-popup__submit">
                        <span v-html="'Отправить'" @click="toggleSecondModal(summ)"/>
                    </a>
                </div>

                <div class="news-gift-popup__footer">
                    <span class="news-gift-popup__button" @click="toggleSecondModal(250)" v-html="'250 KZT'"/>
                    <span class="news-gift-popup__button" @click="toggleSecondModal(500)" v-html="'500 KZT'"/>
                    <span class="news-gift-popup__button" @click="toggleSecondModal(1000)" v-html="'1000 KZT'"/>
                </div>
            </div>
        </div>

        <div v-show="showSecondModal" class="news-gift-second">
            <div class="news-gift-second__container">

                <img class="news-gift-second__close" @click="closeSecondModal()" alt="" src="/icon/news/birthday/close.svg">

                <div class="news-gift-second__header">
                    <img src="/icon/news/birthday/hand.svg">
                    <div class="news-gift-second__text">
                        Вы дарите <span>{{summ + ' KZT'}}</span> пользователю:
                    </div>
                </div>

                <div class="news-gift-second__body">
                    <img
                        class="news-birthday-card__image"
                        :src="user.avatar"
                        alt="img">
                    <div class="news-birthday-card__body">
                        <span class="news-birthday-card__name" v-html="user.name"/>
                        <span :class="'news-birthday-card__birthday ' + getCardColor(user)"
                              v-html="user.date_human"/>
                    </div>
                    <a class="news-gift-second__send">
                        <span @click="sendMoney" v-html="'Подарить!'"/>
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import moment from "moment/moment";


export default {
    name: "BirthdayUser",
    props: {
        user: {
            required: true
        }
    },
    data() {
        return {
            hover: false,
            showModal: false,
            showSecondModal: false,
            summ: '',
            success: false,
        }
    },
    mounted() {

    },
    methods: {
        togleShowModal(newValue) {
            console.log(newValue);
            if (newValue)
            {
                let scrollTop = document.documentElement.scrollTop;
                let scrollLeft = document.documentElement.scrollLeft;
                window.onscroll = function() {
                    window.scrollTo(scrollLeft, scrollTop);
                };
            }
            else{
                this.showSecondModal = false;
                window.onscroll = function() {};
            }

            this.showModal = newValue;
            this.$root.$emit('toggle-white-bg', newValue);
        },

        toggleSecondModal(value) {
            this.summ = value;
            this.showSecondModal = true;
        },

        closeSecondModal() {
            this.showSecondModal = false;
        },

        getCardColor(user) {
            return moment(user.date).format('DD-MM') == moment().format('DD-MM') ? 'news-birthday-card__birthday--today' :
                (moment(user.date).format('DD-MM') == moment().add(1, 'd').format('DD-MM') ? 'news-birthday-card__birthday--tomorrow' : '');
        },

        async sendMoney() {
            let formData = new FormData;
            this.togleShowModal(false);
            this.showSecondModal = false;

            formData.append('amount', this.summ);
            this.summ = '';
            await axios.post('birthdays/' + this.user.id + '/send-gift ', formData)
                .then(res => {
                    console.log(res);
                    this.createAvans(res.data.data)

                })
                .catch(res => {
                    console.log(res);
                })
        },
        async createAvans(data) {

            await axios.post('/timetracking/salaries/update', data.avansData)
                .then(res => {
                  console.log(res)
                  this.sendBonuses(data.bonusData)
                })
                .catch( res =>{
                  console.log(res)
                })
        },
        async sendBonuses(data) {
          await axios.post('/timetracking/salaries/update', data)
                .then(res => {
                  console.log(res)
                  this.success = true;
                  setTimeout(() => {  this.success = false;}, 5000);
                })
                .catch( res =>{
                  console.log(res)
                })
        },

    }
}
</script>
