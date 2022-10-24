<template>
<div class="popup__con">
    <div class="tabs">
        <div class="popup__filter">
            <div class="trainee__tabs tabs__wrapper">
                <div  class="trainee__tab tab__item is-active" onclick="switchTabs(this)"  data-index="1">Новые 
                    <template v-if="data.unreadQuantity != 0">
                        (<span>{{ data.unreadQuantity }}</span>) 
                    </template>
                   
                </div>
                <div class="trainee__tab tab__item" onclick="switchTabs(this)"  data-index="2">Прочитанные</div>
            </div>
        </div>

        <div class="tab__content">
            

            <!-- Unread notifications -->
            <div class="kaspi__content custom-scroll-y tab__content-item is-active"  data-content="1">
                <div class="notifications__wrapper">

                    <div class="notifications__item unread" v-for="(item, i) in data.unread" :key="i">
                        <div class="notifications__item-date">{{ item.created_at }}</div>
                        <div class="notifications__title">
                            {{ item.title }}
                        </div>
                        <div class="notifications__text" v-html="item.message"></div>
                        <div class="notifications__read" @click="setRead(i)"></div>
                    </div>

                    <div v-if="data.unread.length == 0" class="mt-5">
                        <h4>Нет новых уведомлений</h4>
                    </div>
                </div>
            </div>

            <!-- Read notifications -->
            <div class="kaspi__content custom-scroll-y tab__content-item"  data-content="2">
                <div class="notifications__wrapper">

                    <div class="notifications__item" v-for="(item, i) in data.read" :key="i">
                        <div class="notifications__item-date">{{ item.created_at }}</div>
                        <div class="notifications__title">
                            {{ item.title }}
                        </div>
                        <div class="notifications__text" v-html="item.message"></div>
                        <div class="notifications__item-date absolute">{{ item.read_at }}</div>
                    </div>

                    <div v-if="data.unread.length == 0" class="mt-5">
                        <h4>Нет прочитанных уведомлений</h4>
                    </div>
                </div>

                <a href="#" class="notifications__button mt-5" @click="setAllRead">
                    Отметить все как прочитанное
                </a>
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
            data: {
                unreadQuantity: 0,
                unread: [],
                read: [],
            },
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
            console.log('set read all')
        },

        /**
         * set notification as read
         */
        setRead(i) {
            console.log(this.data.unread[i])
        }
    }
};
</script>