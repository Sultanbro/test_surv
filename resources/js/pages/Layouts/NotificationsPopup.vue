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

                    <div class="notifications__item" v-for="(item, i) in data.unread" :key="i">
                        <div class="notifications__item-date">{{ $moment(item.created_at).format(dateFormat) }}</div>
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

                <a href="#" class="notifications__button mt-5" @click="setAllRead" v-if="data.unread.length > 0">
                    Отметить все как прочитанное
                </a>
            </div>

            <!-- Read notifications -->
            <div class="kaspi__content custom-scroll-y tab__content-item"  data-content="2">
                <div class="notifications__wrapper">

                    <div class="notifications__item" v-for="(item, i) in data.read" :key="i">
                        <div class="notifications__item-date">{{ $moment(item.created_at).format(dateFormat) }}</div>
                        <div class="notifications__title">
                            {{ item.title }}
                        </div>
                        <div class="notifications__text" v-html="item.message"></div>
                        <div class="notifications__item-date absolute">{{ $moment(item.read_at).format(dateFormat) }}</div>
                    </div>

                    <div v-if="data.read.length == 0" class="mt-5">
                        <h4>Нет прочитанных уведомлений</h4>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
</template>

<script>
export default {
    name: 'NotificationsPopup', 
    props: {},
    data: function () {
        return {
            dateFormat: 'DD.MM.YYYY',
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
            
            axios.post('/notifications', {})
                .then((response) => {
                    this.data = response.data
                    console.log(this.data)
                    loader.hide();
                }).catch(e => {
                    console.log(e)
                    loader.hide();
                });
        },

        /**
         * Set all notifications as read
         */
        setAllRead() {

            let loader = this.$loading.show();
            
            axios.post('/notifications/set-read-all/', {})
                .then((response) => {
                    if(response.data == '1') {

                        this.data.unread.forEach(el => {
                            this.data.read.push(el)
                        });

                        this.data.unread = [];
                        this.data.unreadQuantity = 0;
                        

                        this.$toast.success('Все уведомления отмечены прочитанными');
                    }
                    loader.hide();
                }).catch(e => {
                    console.log(e)
                    loader.hide();
                });
        },

        /**
         * set notification as read
         */
        setRead(i) {
            console.log(this.data.unread[i])

            this.req(i, {
                id: this.data.unread[i]
            })
        },

        /**
         * Сохранить отчет
         */
        saveReport(i) {
            // let payload = {
            //     id: this.data.unread[i].id, 
            //     comment: 0,
            //     type: 'report',
            //     text: 'text of report'
            // };
            // this.req(i, payload)
        },

        /**
         * Перенос обучения (дня стажировки на другой день)
         */
        transferTraining(i) {
            // let payload = {
            //     user_id: user_id,
            //     date: '2022-09-01',
            //     time: '14:00',
            //     id: this.data.unread[i].id, 
            //     type: 'transfer',
            // };
            // this.req(i, payload)
        },

        /**
         * Сохранить причину отсутствия и дать оценку руководителю 
         */
        estimateTrainer(i) {
            // let payload = {
            //     id: this.data.unread[i].id, 
            //     comment: 'Комментарий из select',
            // }
            // this.req(i, payload)
        },

        /**
         * set Read request 
         */
        req(i, payload) {
            let loader = this.$loading.show();
            
            axios.post('/notifications/set-read', payload)
                .then((response) => {
                    if(response.data == 1) {

                        this.data.read.unshift(this.data.unread[i])
                        this.data.unread.splice(i, 1);

                        this.data.unreadQuantity--;
                        this.$toast.success('Уведомление прочитано');

                        // $('#setReadCommentModal').fadeOut();
                        // $('#setReadReportModal').fadeOut();
                        // nullify(dateForTransfer);
                    }

                    loader.hide();
                }).catch(e => {
                    console.log(e)
                    loader.hide();
                });
        }
    }
};
</script>