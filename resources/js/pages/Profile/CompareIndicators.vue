<template>
<div
    id="index"
    class="index block _anim _anim-no-hide content"
    :class="{
        'hidden': items.length == 0,
        'v-loading': loading
    }"
>
    <div class="title index__title mt-5">
        Сравнение показателей
    </div>
    <div class="subtitle index__subtitle">
        Сравните Ваши показатели с другими сотрудниками
    </div>

    <div class="index__table">
        <b-tabs>
            <b-tab
                v-for="(item, itemIndex) in items"
                :key="itemIndex"
                :title="item.group.name"
            >
                <div class="tabs">
                    <div class="index__tabs tabs__wrapper">
                        <div
                            v-for="(act, index) in item.activities"
                            :key="index"
                            onclick="switchTabs(this)"
                            class="index__tab tab__item"
                            :class="{'is-active': index == 0}"
                            :data-index="index"
                        >
                            {{ act.name }}
                        </div>
                    </div>
                    <select class="select-css trainee-select mobile-select">
                        <option v-for="(act, index) in item.activities"
                            :value="index"
                            :key="index"
                        >
                            {{ act.name }}
                        </option>
                    </select>
                    <div class="tab__content">
                        <div
                            v-for="(act, index) in item.activities"
                            class="tab__content-item index__content"
                            :class="{'is-active': index == 0}"
                            :data-content="index"
                            :key="index"
                        >
                            <Collection
                                v-if="act.type == 'collection'"
                                :key="act.id"
                                :month="monthInfo"
                                :activity="act"
                                :is_admin="false"
                                :price="act.price"
                            />
                            <Default
                                v-else-if="act.type == 'default'"
                                :key="'d'+act.id"
                                :month="monthInfo"
                                :activity="act"
                                :group_id="act.group_id"
                                :work_days="act.workdays"
                                :editable="false"
                                :show_headers="false"
                            />
                            <Quality
                                v-else-if="act.type == 'quality'"
                                :key="'q'+act.id"
                                :monthInfo="monthInfo"
                                :items="act.records"
                            />

                            <!-- <tr class="prize first-place">
                            <tr class="prize second-place" >
                            <tr class="prize third-place">
                            <td><div class="large">Аппазова Карлыгаш</div></td>
                            <td><div class="medium">233</div></td>
                            <td><div class="medium">7020</div></td>
                            <td><div class="small">43.11</div></td>
                            <td class="red"><div>3026.00</div></td>
                            <td class="green"><div>3026.00</div></td>
                            <td class="blue"><div>3026.00</div></td> -->
                        </div>
                    </div>
                </div>
            </b-tab>
        </b-tabs>
    </div>
</div>
</template>

<script>
import Collection from '@/pages/Tables/Collection.vue'
import Default from '@/pages/Tables/Default.vue'
import Quality from '@/pages/Tables/Quality.vue'

export default {
    name: 'CompareIndicators',
    components: {
        Collection,
        Default,
        Quality,
    },
    props: {},
    data: function () {
        return {
            items: [],
            currentYear: new Date().getFullYear(),
            monthInfo: {
                currentMonth: null,
                monthEnd: 0,
                workDays: 0,
                weekDays: 0,
                workDays5: 0,
                weekDays5: 0,
                daysInMonth: 0,
                year: new Date().getFullYear()
            },

            loading: false
        };
    },
    created() {
        this.setMonthInfo()
        this.createConsts()
        this.fetchData()
    },

    methods: {
        /**
         * Загрузка данных
         */
        fetchData() {
            this.loading = true

            axios.post('/profile/activities').then(response => {

                this.items = response.data.items

                this.showBtn(response.data)
                this.loading = false
            }).catch((e) => console.log(e))
        },

        /**
         * private: show btn in introTop
         */
        showBtn(data) {
            const totalActivities = data.items.reduce((n, item) => {
                if(item.activities.length === 1 && item.activities[0].name === 'OKK') return n
                return n + item.activities.length
            }, 0)
            if(totalActivities > 0) {
                this.$emit('init')
            }
        },

        /**
         * private: consts for activity
         */
        createConsts() {
            this.VIEW_DEFAULT = 0;
            this.VIEW_COLLECTION = 1;
            this.VIEW_QUALITY = 2;
            this.VIEW_RENTAB = 3;
            this.VIEW_TURNOVER = 4;
            this.VIEW_STAFF = 5;
            this.VIEW_CONVERSION = 6;
            this.VIEW_CELL = 7;
        },

        /**
         * private: prepare month table
         */
        setMonthInfo() {
            this.monthInfo.currentMonth = this.monthInfo.currentMonth ? this.monthInfo.currentMonth : this.$moment().format('MMMM')
            this.monthInfo.month = this.monthInfo.currentMonth ? this.$moment(this.monthInfo.currentMonth, 'MMMM').format('M') : this.$moment().format('M')
            let currentMonth = this.$moment(this.monthInfo.currentMonth, 'MMMM')
            //Расчет выходных дней
            this.monthInfo.monthEnd = currentMonth.endOf('month'); //Конец месяца
            this.monthInfo.weekDays = currentMonth.weekdayCalc(currentMonth.startOf('month').toString(), currentMonth.endOf('month').toString(), [6]) //Колличество выходных
            this.monthInfo.weekDays5 = currentMonth.weekdayCalc(currentMonth.startOf('month').toString(), currentMonth.endOf('month').toString(), [6,0]) //Колличество выходных
            this.monthInfo.daysInMonth = new Date(this.$moment().format('YYYY'), this.$moment(this.monthInfo.currentMonth, 'MMMM').format('M'), 0).getDate() //Колличество дней в месяце
            this.monthInfo.workDays = this.monthInfo.daysInMonth - this.monthInfo.weekDays //Колличество рабочих дней
            this.monthInfo.workDays5 = this.monthInfo.daysInMonth - this.monthInfo.weekDays5 //Колличество рабочих дней

            this.currentYear = this.$moment().format('YYYY') //Установка выбранного года
            this.monthInfo.currentYear = this.currentYear;
        },
    }
};
</script>

<style lang="scss">
.index__table{
    >.tabs{
        margin-top: -3rem;
    }
    .nav-tabs{
        gap: 0 4rem;
        padding-bottom: 0.5rem;
        border: none;
        .nav-link{
            &.active{
                border: none;
                color: #ED2353;
                background: none;
            }
        }
    }
    .nav-item{
        margin-top: -0.1rem;
    }
    .nav-link{
        padding: 1.5rem 0 0;
        border: none;
        background: none;
        border-radius: 0;

        line-height: 2em;
        color: #8D8D8D;
        font-size: 1.7rem;
        font-family: "Open Sans", sans-serif;
        font-weight: 600;
        transition: color 0.3s;
        cursor: pointer;

        &:hover{
            color: #ED2353;
        }
    }
    .index__tab{
        padding-top: 0;
    }
}

@media(max-width:440px){
    .index__table{
        >.tabs{
            margin-top: -2rem;
        }
    }
}
</style>