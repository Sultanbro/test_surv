<template>
<div class="header__profile _anim _anim-no-hide custom-scroll-y">
    <div class="profile__content">
        <a href="#" class="profile__logo" v-if="$laravel.userId == 5 || $laravel.userId == 18" @click="uploadLogo">
            <img src="/images/dist/logo-download.svg" alt="logo download">
            Загрузить логотип
        </a>

       

        <start-day-btn @currentBalance="currentBalance"></start-day-btn>

        <div class="profile__balance">
            Текущий баланс
            <p>{{ balance }} <span>{{ currency }}</span></p>
        </div>

        
        <profile-info></profile-info>
<!--         
        <div class="profile__point profile-box">
            <div class="profile__title">Цель на сегодня</div>
            <div class="profile__point-wrapper profile-slick">
                <div class="profile__point-start">
                    <p>Начало рабочего дня</p>
                    <p>10 : 20 am</p>
                    <div class="profile__point-time">
                        <div class="profile__time-row">
                            <p class="blue time spent">1 h 23 m</p>
                            <p>пройдено</p>
                        </div>
                        <div class="profile__time-row">
                            <p class="time left">0 h 34 m</p>
                            <p>осталось</p>
                        </div>
                    </div>
                </div>
                <div class="profile__progressbar">
                    <svg class="progress-ring" width="80" height="80">
                        <circle stroke="#fff" stroke-width="8" cx="40" cy="40" r="30" fill="#8FAF00"/>
                        <circle class="progress-ring__circle" stroke="rgba(96,142,233,0.5)" stroke-width="4" cx="40" cy="40" r="36" fill="transparent"/>
                    </svg>
                    <div class="profile__progressbar-number">
                        <span>87</span>%
                    </div>
                </div>
            </div>
            <img src="images/dist/close.svg" alt="close icon" class="point-close">
        </div> -->

        <div class="profile__active profile-box">
            <div class="profile__title">График активности</div>
            <div class="tabs__include profile-slick">
                <div class="tab__content-include">
                    <div class="tab__content-item-include is-active"  data-content="1">
                        <img src="images/dist/schedule.png" alt="schedule image">
                    </div>
                    <div class="tab__content-item-include"  data-content="2">
                        <img src="images/dist/profile-active.png" alt="schedule image">
                    </div>
                    <div class="tab__content-item-include"  data-content="3">
                        <img src="images/dist/schedule.png" alt="schedule image">
                    </div>
                </div>
                <div class="tabs__wrapper">
                    <div  class="tab__item-include is-active" onclick="switchTabsInclude(this)"  data-index="1">День</div>
                    <div  class="tab__item-include" onclick="switchTabsInclude(this)"  data-index="2">месяц</div>
                    <div  class="tab__item-include" onclick="switchTabsInclude(this)"  data-index="3">год</div>
                </div>
            </div>
            <img src="images/dist/close.svg" alt="close icon" class="point-close">
        </div>

        <!-- <a href="#" class="profile__more" v-if="$laravel.userId == 5 || $laravel.userId == 18" @click="addWidget">
            <img src="/images/dist/logo-download.svg" alt="more download">
        </a> -->
    </div>
</div>
</template>

<script>
export default {
    name: "ProfileSidebar", 
    props: {},
    data: function () {
        return {
            fields: [], 
            balance: 0,
            currency: 'KZT'
        };
    },
    created() {
        
    },
    methods: {
        /**
         * Загрузить лого
         */
        uploadLogo() {
            alert('Вы не можете, потому что не работает');
        },

        /**
         * Добавить виджет
         */
        addWidget() {
            alert('Вы не можете, потому что не работают Виджеты');
        },

        /**
         * callback c startDayBtn
         * возвращает текущий баланс
         */
        currentBalance(balance) {
            this.balance = balance.sum
            this.currency = balance.currency
        },


        
    }
};
</script>