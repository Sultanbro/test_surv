<template>
<div class="popup__content">
    <div class="popup__filter">
        <select class="select-css" v-model="currentMonth" @change="fetchBefore()">
            <option
                v-for="month in $moment.months()"
                :value="month"
                :key="month"
            >
                {{ month }}
            </option>
        </select>
    </div>
    <div class="kpi__content">
        <div class="kpi__kaspi">
            <div class="kpi__title popup__content-title">
                Kaspi
            </div>
            <div class="kpi__kaspi-wrapper">
                <div class="kpi__kaspi-left">
                    <table>
                        <tr>
                            <td class="blue">Выполнение KPI от 80-99%</td>
                            <td>20.000</td>
                        </tr>
                        <tr>
                            <td class="blue">Выполнение KPI на 100%</td>
                            <td >40.000</td>
                        </tr>
                    </table>
                </div>
                <div class="kpi__kaspi-right">
                    <table>
                        <thead>
                            <tr>
                                <th>Нижний порог отсечения премии, %</th>
                                <th>Верхний порог отсечения премии, %</th>
                            </tr>
                        </thead>
                        <tr>
                            <td>80</td>
                            <td >100</td>
                        </tr>
                    </table>
                </div>
            </div>

        </div>

        <div class="kpi__activities">
            <div class="kpi__title popup__content-title">
                Активности KPI
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>Наименование активности</th>
                        <th>Целевое значение
                            за мес</th>
                        <th>Выполнено</th>
                        <th>Удельный
                            вес %</th>
                        <th>Сумма премии при
                            выполнении плана, тг</th>
                        <th>% выполнения</th>
                        <th>Сумма премии за
                            % выполнения</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Минуты разговора</td>
                        <td>7020</td>
                        <td>0</td>
                        <td>70</td>
                        <td>28.000</td>
                        <td>0.00</td>
                        <td>0.0</td>
                    </tr>
                    <tr>
                        <td>Оценка диалога</td>
                        <td>100%</td>
                        <td>0</td>
                        <td>30</td>
                        <td>12.000</td>
                        <td>0.00</td>
                        <td>0.0</td>
                    </tr>
                    <tr>
                        <td class="none"></td>
                        <td class="none"></td>
                        <td class="none"></td>
                        <td class="none"></td>
                        <td class="none"></td>
                        <td class="none"></td>
                        <td><b>0</b></td>
                    </tr>
                </tbody>
            </table>

            <div class="kpi__activities-tip">
                * сумма премии за выполнение показателей начнет меняться после достижения 80% от целевого значения на месяц
            </div>
        </div>

    </div>
</div>
</template>

<script>
export default {
    name: "PopupKpi", 
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
        this.fetchData({
            data_from: {
                year: new Date().getFullYear(),
                month: this.$moment(this.currentMonth, 'MMMM').format('M')
            },
            user_id: 5
        })
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

        fetchBefore() {
            this.fetchData({
                data_from: {
                    year: new Date().getFullYear(),
                    month: this.$moment(this.currentMonth, 'MMMM').format('M')
                },
                user_id: 5
            })
        },

        fetchData(filters = null) {
            let loader = this.$loading.show();

            axios.post('/statistics/kpi', {
                filters: filters 
            }).then(response => {
                
                // items
                this.kpis = response.data.items;
                this.kpis = this.kpis.map(res=> ({...res, my_sum: 0}))
                
                this.activities = response.data.activities;
                this.groups = response.data.groups;

                loader.hide()
            }).catch(error => {
                loader.hide()
                alert(error)
            });
        }
    }
};
</script>