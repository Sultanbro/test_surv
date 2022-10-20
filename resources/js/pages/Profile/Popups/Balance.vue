<template>
<div class="popup balance js-popup">
    <div class="popup__content">
        <div class="popup-header">
            <a class="popup-close js-close-popup" href="#" ><img src="images/dist/popup-close.svg" alt="Close icon" ></a>
            <div class="popup__header-content">
                <div class="popup__title">
                    Баланс оклада
                </div>
                <div class="popup__subtitle">
                    Дополнительное поле с описанием функционала данного окна
                </div>
            </div>
        </div>
        <div class="popup__body">
            <div class="popup__filter">
                <div class="popup__filter-title">
                    Ваши начисления за период работы
                </div>

                <select class="select-css" v-model="dateInfo.currentMonth" @change="fetchData()">
                    <option
                        v-for="month in $moment.months()"
                        :value="month"
                        :key="month"
                    >
                        {{ month }}
                    </option>
                </select>


            </div>
            <div class="balance__content custom-scroll">
                <table class="balance__table">
                    <thead>
                        <tr>
                            <th v-for="field in fields" :class="{
                                    'text-center': field.key != '0',
                                }">
                                
                                {{ field.label }}

                                <i class="fa fa-info-circle" v-if="field.key == 'avanses'"
                                        v-b-popover.hover.right.html="'Авансы отмечены зеленым'">
                                </i>
                                <i class="fa fa-info-circle" v-if="field.key == 'fines'"
                                    v-b-popover.hover.right.html="'Депримирование отмечено красным'">
                                </i>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in items">
                            <td v-for="field in fields" 
                                :class="{
                                    'day-fine':item[field.key] !== undefined && item[field.key].hasFine,
                                    'day-avans': item[field.key] !== undefined && item[field.key].hasAvans,
                                    'day-bonus': item[field.key] !== undefined && item[field.key].hasBonus,
                                    'text-center': field.key != '0',
                                    'day-training': item[field.key] !== undefined && item[field.key].training,
                                }"
                            >
                                <template v-if="item[field.key] !== undefined">
                                    {{ item[field.key].value }}
                                </template>
                            </td>
                        </tr>
                    </tbody>
                </table>
                    <!--						<div class="balance__title">-->
                    <!--							ИСТОРИЯ-->
                    <!--						</div>-->
                    <!--						<div class="balance__inner">-->
                    <!--							<div class="balance__item">-->
                    <!--								<div class="balance__item-title">Начислено</div>-->
                    <!--								<div class="balance__item-value">0</div>-->
                    <!--							</div>-->
                    <!--							<div class="balance__item">-->
                    <!--								<div class="balance__item-title">Депремирование</div>-->
                    <!--								<div class="balance__item-value">Нет штрафов</div>-->
                    <!--							</div>-->
                    <!--							<div class="balance__item">-->
                    <!--								<div class="balance__item-title">Бонусы</div>-->
                    <!--								<div class="balance__item-value">Нет бонусов </div>-->
                    <!--							</div>-->
                    <!--							<div class="balance__item">-->
                    <!--								<div class="balance__item-title">Авансы</div>-->
                    <!--								<div class="balance__item-value">Нет авансов</div>-->
                    <!--							</div>-->
                    <!--						</div>-->
            </div>

        </div>
    </div>
</div>
</template>

<script>
export default {
    name: "PopupBalance", 
    props: {},
    data: function () {
        return {
            data: [], 
            items: [],
            totalFines: null, 
            total_avanses: null, 
            fields: [],
            dateInfo: {
                currentMonth: null,
                monthEnd: 0,
                workDays: 0,
                weekDays: 0,
                daysInMonth: 0
            },
        };
    },
    created() {
        this.setMonth()
        this.setFields()
        this.fetchData()
    },
    methods: {
        /**
         * set month
         */
        setMonth() {
            let year = moment().format('YYYY')
            this.dateInfo.currentMonth = this.dateInfo.currentMonth ? this.dateInfo.currentMonth : this.$moment().format('MMMM')
            this.dateInfo.date = `${this.dateInfo.currentMonth} ${year}`

            let currentMonth = this.$moment(this.dateInfo.currentMonth, 'MMMM')

            //Расчет выходных дней
            this.dateInfo.monthEnd = currentMonth.endOf('month'); //Конец месяца
            this.dateInfo.weekDays = currentMonth.weekdayCalc(this.dateInfo.monthEnd, [6]) //Колличество выходных
            this.dateInfo.daysInMonth = currentMonth.daysInMonth() //Колличество дней в месяце
            this.dateInfo.workDays = this.dateInfo.daysInMonth - this.dateInfo.weekDays //Колличество рабочих дней
        },
        /**
         * Загрузка данных для таблицы
         */
        fetchData() {
            let loader = this.$loading.show();

            axios.post('/timetracking/zarplata-table', {
                month: new Date().getMonth() + 1,
            }).then(response => {

                this.data = response.data.data
                this.totalFines = response.data.totalFines
                this.total_avanses = response.data.total_avanses
                
                this.loadItems()
                
                loader.hide()
            }).catch((e) => console.log(e))
        },

        loadItems() {
            let items = [];
            let temp = [];
            let total = {
                'salaries':0,
                'hours':0,
            };

            for (let key in this.data) {
                temp[key] = []
                for (let keyt in this.data[key]) {
                    temp[key][keyt] = ({
                        'value': this.data[key][keyt]['value'],
                        'fines': this.data[key][keyt]['fines'],
                        'avanses': this.data[key][keyt]['avanses'],
                        'bonuses': this.data[key][keyt]['bonuses'],
                        'test_bonus': this.data[key][keyt]['test_bonus'],
                        'awards': this.data[key][keyt]['awards'],
                        'hasFine': this.data[key][keyt]['fines'] !== undefined && this.data[key][keyt]['fines'].length,
                        'hasBonus': (this.data[key][keyt]['bonuses'] !== undefined && this.data[key][keyt]['bonuses'].length) || (this.data[key][keyt]['awards'] !== undefined && this.data[key][keyt]['awards'].length)
                            || (this.data[key][keyt]['test_bonus'] !== undefined && this.data[key][keyt]['test_bonus'].length),
                        'hasAvans': this.data[key][keyt]['avanses'] !== undefined && this.data[key][keyt]['avanses'].length,
                        'training': this.data[key][keyt]['training'],
                    })

                    if(key == 'salaries' || key == 'hours') {
                        let val = Number(this.data[key][keyt]['value']);
                        total[key] += isNaN(val) ? 0 : val;
                    }
                }
            }

            temp['salaries'][0] = {
                'value': 'Начисления',
            };

            let total_salary = 0;
                total_salary = Number(total['salaries']) - Number(this.totalFines) - Number(this.total_avanses);

            temp['salaries']['total'] = {
                'value': Number(total_salary).toFixed(0),
            };
            temp['salaries']['avanses'] = {
              'value': Number(this.total_avanses).toFixed(0)
            };
            temp['salaries']['fines'] = {
                'value': Number(this.totalFines).toFixed(0)
            };


            temp['times'][0] = {
                'value': 'Время прихода',
            };
            temp['hours'][0] = {
                'value': 'Отработанные часы',
            };
            temp['hours']['total'] = {
                'value': Number(total['hours']).toFixed(1),
            };
            temp['times']['avanses'] = {
              'value': 0
            };
            temp['times']['fines']= {
              'value': 0
            };
            temp['hours']['avanses'] = {
              'value': 0
            };
            temp['hours']['fines'] = {
              'value': 0
            };
            items.push(temp['times'])
            items.push(temp['salaries'])
            items.push(temp['hours'])
            this.items = items
        },

        // Установка заголовка таблицы
        setFields() {
            let fields = []

            fields = [
                {
                    key: "0",
                    label: "Дни",
                    variant: "title",
                    class: "text-left t-name"
                },
                {
                    key: "total",
                    label: "К выдаче",
                    variant: "title",
                    class: "text-center t-name"
                },
                {
                  key: "avanses",
                  label: "Авансы",
                  variant: "title",
                  class: "text-center t-name"
                },
                {
                  key: "fines",
                  label: "Штрафы",
                  variant: "title",
                  class: "text-center t-name"
                }
            ];

            let days = this.dateInfo.daysInMonth

            for (let i = 1; i <= days; i++) {
                let dayName = this.$moment(`${i} ${this.dateInfo.date}`, 'D MMMM YYYY').locale('en').format('ddd')
                fields.push({
                    key: `${i}`,
                    label: `${i}`,
                    sortable: false,
                    class: `day ${dayName}`,
                })
            }
            this.fields = fields
        },
    }
};
</script>

<style lang="scss">
$fine: #e84f71;
$avans: #8bab00;
$bonus: #8fc9ff;
$training: #f90;

.fz-09 {
    font-size: 0.9rem;
}

.fine,.avans,.bonus {
    color:#fff;
}

.balance__table {
    .day-fine {
        background: $fine;
        color: #fff;
    }
    .day-training {
        background: $training;
        color: #fff;
        &.day-fine {background: linear-gradient(110deg, $training 50%, $fine 50%);}
    }
    .day-bonus {
        background: $bonus;
        color: #fff;
        &.day-fine {background: linear-gradient(110deg, $bonus 50%, $fine 50%);}
        &.day-training {background: linear-gradient(110deg, $bonus 50%, $training 50%);}
        &.day-fine.day-training {background: linear-gradient(110deg, $bonus 33%, transparent 33%), linear-gradient(110deg, $fine 66%, $training 66%);}
    }
    .day-avans {
        background:$avans;
        color: #fff;
        &.day-fine {background: linear-gradient(110deg, $avans 50%, $fine 50%);}
        &.day-bonus {background: linear-gradient(110deg, $avans 50%, $bonus 50%);}
        &.day-training {background: linear-gradient(110deg, $avans 50%, $training 50%);}
        &.day-bonus.day-fine {background: linear-gradient(110deg, $avans 33%, transparent 33%), linear-gradient(110deg, $bonus 66%, $fine 66%);}
        &.day-bonus.day-fine.day-training {background: linear-gradient(110deg, $avans 25%, transparent 25%), linear-gradient(110deg, $bonus 50%, $fine 50%), linear-gradient(110deg, $fine 75%, $training 75%);}
    }
}

/**
    Не относится к этому для tooltip bootstrap
*/
.popover {
    z-index: 999999;
}
</style>