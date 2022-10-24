<template>
<div class="popup__body">
    <div class="tabs">
        <div class="popup__filter">
            <div class="trainee__tabs tabs__wrapper">
                <div  class="trainee__tab tab__item is-active" onclick="switchTabs(this)"  data-index="1">Уведомления (<span>14</span>)</div>
                <div  class="trainee__tab tab__item" onclick="switchTabs(this)"  data-index="2">Уведомления прочитанные</div>
            </div>
        </div>

        <div class="tab__content">
            <div class="kaspi__content custom-scroll-y tab__content-item is-active"  data-content="1">
                <div class="notifications__wrapper">

                    <div class="notifications__item">
                        <div class="notifications__item-date">10.07.2022 13:02:15</div>
                        <div class="notifications__title">
                            Изменения в базе знаний
                        </div>
                        <div class="notifications__text">
                            <p>База знаний: Глоссарий №1</p>
                            <p>Страница: <span class="red">Тестировщик</span></p>
                        </div>
                        <div class="notifications__item-date absolute">10.07.2022 13:02:15</div>
                    </div>

                </div>

                <a href="#" class="notifications__button" @click="setAllRead">
                    Отметить все как прочитанное
                </a>
            </div>

            <div class="kaspi__content custom-scroll-y tab__content-item"  data-content="2">
                <div class="notifications__wrapper">

                    <div class="notifications__item unread">
                        <div class="notifications__item-date">10.07.2022 13:02:15</div>
                        <div class="notifications__title">
                            Изменения в базе знаний
                        </div>
                        <div class="notifications__text">
                            <p>База знаний: Глоссарий №1</p>
                            <p>Страница: <span class="red">Тестировщик</span></p>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
</template>

<script>
export default {
    name: "NotificationsPopup", 
    props: {},
    data: function () {
        return {
            data: [],
        };
    },
    created(){
        this.fetchData()
    },
    methods: {
        fetchData() {
            let loader = this.$loading.show();
            
            axios.post("/notifications", {})
                .then((response) => {
                    this.data = response.data
                    console.log(this.data)
                    loader.hide();
                });
        },

        /**
         * Set all notifications as read
         */
        setAllRead() {

        },

        /**
         * set notification as read
         */
        setRead(id) {

        }
    }
};
</script>