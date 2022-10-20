<template>
<div class="popup__content mt-3">
    <div class="popup__filter">
        <select class="select-css" v-model="currentMonth" @change="fetchData()">
            <option
                v-for="month in $moment.months()"
                :value="month"
                :key="month"
            >
                {{ month }}
            </option>
        </select>
    </div>
    <div class="popup__award">
        <div class="award__title popup__content-title">
            За период с 01.03.2020 до 31.06.2022
        </div>
        <table class="award__table">
            <tr>
                <td class="blue">Сумма</td>
                <td>400.000</td>
            </tr>
            <tr>
                <td class="blue">Комментарии</td>
                <td>Нужно выполнить условие: 1.хх 2.хх</td>
            </tr>
        </table>
    </div>
</div>
</template>

<script>
export default {
    name: "PopupQuartal", 
    props: {},
    data: function () {
        return {
            fields: [], 
            currentMonth: null,
            dateInfo: {
                currentMonth: null,
                monthEnd: 0,
                workDays: 0,
                weekDays: 0,
                daysInMonth: 0
            },
        };
    },
    created(){
        this.setMonth()
        this.fetchData()
    },
    methods: {
        /**
         * set month
         */
        setMonth() {
            let year = moment().format('YYYY')
            this.dateInfo.currentMonth = this.dateInfo.currentMonth ? this.dateInfo.currentMonth : this.$moment().format('MMMM')
            this.currentMonth = this.dateInfo.currentMonth;
            this.dateInfo.date = `${this.dateInfo.currentMonth} ${year}`

            let currentMonth = this.$moment(this.dateInfo.currentMonth, 'MMMM')

            //Расчет выходных дней
            this.dateInfo.monthEnd = currentMonth.endOf('month'); //Конец месяца
            this.dateInfo.weekDays = currentMonth.weekdayCalc(this.dateInfo.monthEnd, [6]) //Колличество выходных
            this.dateInfo.daysInMonth = currentMonth.daysInMonth() //Колличество дней в месяце
            this.dateInfo.workDays = this.dateInfo.daysInMonth - this.dateInfo.weekDays //Колличество рабочих дней
        },

        fetchData() {

        }
    }
};
</script>