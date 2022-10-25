<template>
<div class="index block _anim _anim-no-hide content" id="index" :class="{'hidden': activities.length == 0}">
    <div class="title index__title mt-5">
        Сравнение показателей
    </div>
    <div class="subtitle index__subtitle">
        Сравните Ваши показатели с другими сотрудниками
    </div>

    <div class="index__table">
        <div class="tabs custom-scroll">
            <div class="index__tabs tabs__wrapper">
                <div class="index__tab tab__item"
                    v-for="(act, index) in activities"
                    :key="index"
                    onclick="switchTabs(this)"
                    :class="{'is-active': index == 0}"
                    :data-index="index"
                >
                    {{ act.name }}
                </div>
            </div>
            <select class="select-css trainee-select mobile-select">
                <option v-for="(act, index) in activities"
                    :value="index"
                    :key="index"
                >
                    {{ act.name }}
                </option>
            </select>
            <div class="tab__content">


                <div v-for="(act, index) in activities" 
                    class="tab__content-item index__content"
                    :class="{'is-active': index == 0}"
                    :data-content="index" 
                    :key="index"
                >   
                    <t-collection v-if="act.type == 'collection'"
                        :month="monthInfo"
                        :activity="act"
                        :is_admin="false"
                        :key="act.id"
                        :price="act.price"
                    ></t-collection>

                    <t-default  v-else-if="act.type == 'default'"
                        :month="monthInfo"
                        :activity="act"
                        :key="act.id"
                        :group_id="act.group_id"
                        :work_days="act.workdays"
                        :editable="false"
                        :show_headers="false"
                    ></t-default>

                    <t-quality-new v-else-if="act.type == 'quality'"
                        :monthInfo="monthInfo"
                        :items="act.records"
                    ></t-quality-new>
            
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
    </div>


</div>
</template>

<script>
export default {
    name: "CompareIndicators", 
    props: {},
    data: function () {
        return {
            activities: [], 
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
            let loader = this.$loading.show();

            axios.post('/profile/activities').then(response => {

                this.activities = response.data.activities

                loader.hide()
            }).catch((e) => console.log(e))
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