<template>
<div class="popup__content">
    <div class="popup__filter-title">
        Зарабатывайте бонусы, выполняя дополнительные активности
    </div>

    <div class="tabs ">
        <div class="popup__filter">
            <div class="trainee__tabs tabs__wrapper">
                <div  class="trainee__tab tab__item is-active" onclick="switchTabs(this)"  data-index="1">Заработанные бонусы</div>
                <div  class="trainee__tab tab__item" onclick="switchTabs(this)"  data-index="2">Можно заработать</div>
            </div>
            <select class="select-css trainee-select mobile-select">
                <option value="1">Заработанные бонусы</option>
                <option value="2">Можно заработать</option>
            </select>

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

        <div class="tab__content">
            <div class="kaspi__content custom-scroll-y tab__content-item is-active"  data-content="1">
                <table>
                    <thead>
                        <tr>
                            <th>Дата</th>
                            <th>Бонус</th>
                            <th>Комментарии</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>22-06-14</td>
                            <td>1700</td>
                            <td><p>Кол-во сборов 3-6 НБ: 17;</p>
                                <p>Кол-во сборов 3-6 НБ: 17;</p>
                                <p>Кол-во сборов 3-6 НБ: 17;</p>
                                <p>Кол-во сборов 3-6 НБ: 17;</p>
                                <p>Кол-во сборов 3-6 НБ: 17;</p>
                                <p>Кол-во сборов 3-6 НБ: 17;</p></td>
                        </tr>
                        <tr>
                            <td>22-06-14</td>
                            <td>1700</td>
                            <td><p>Кол-во сборов 3-6 НБ: 17;</p>
                                <p>Кол-во сборов 3-6 НБ: 17;</p>
                                <p>Кол-во сборов 3-6 НБ: 17;</p>
                                <p>Кол-во сборов 3-6 НБ: 17;</p>
                                <p>Кол-во сборов 3-6 НБ: 17;</p>
                                <p>Кол-во сборов 3-6 НБ: 17;</p></td>
                        </tr>
                        <tr class="green">
                            <td>22-06-14</td>
                            <td>1700</td>
                            <td><p>Кол-во сборов 3-6 НБ: 17;</p>
                                <p>Кол-во сборов 3-6 НБ: 17;</p>
                                <p>Кол-во сборов 3-6 НБ: 17;</p>
                                <p>Кол-во сборов 3-6 НБ: 17;</p>
                                <p>Кол-во сборов 3-6 НБ: 17;</p>
                                <p>Кол-во сборов 3-6 НБ: 17;</p></td>
                        </tr>
                        <tr>
                            <td>22-06-14</td>
                            <td>1700</td>
                            <td><p>Кол-во сборов 3-6 НБ: 17;</p>
                                <p>Кол-во сборов 3-6 НБ: 17;</p>
                                <p>Кол-во сборов 3-6 НБ: 17;</p>
                                <p>Кол-во сборов 3-6 НБ: 17;</p>
                                <p>Кол-во сборов 3-6 НБ: 17;</p>
                                <p>Кол-во сборов 3-6 НБ: 17;</p></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="kaspi__content custom-scroll-y tab__content-item"  data-content="2">
                <div class="kaspi__wrapper">
                    <div class="kaspi__item">
                        <img src="images/dist/kaspi-gift-green.png" alt="" class="kaspi__item-img">
                        <div class="kaspi__item-about">
                            <div class="kaspi__item-title">
                                100 KZT
                            </div>
                            <div class="kaspi__item-text">
                                За каждую единицу сбора
                            </div>
                        </div>
                    </div>
                    <div class="kaspi__item">
                        <img src="images/dist/kaspi-gift-green.png" alt="" class="kaspi__item-img">
                        <div class="kaspi__item-about">
                            <div class="kaspi__item-title">
                                200 KZT
                            </div>
                            <div class="kaspi__item-text">
                                За каждую единицу сбора
                            </div>
                        </div>
                    </div>
                    <div class="kaspi__item">
                        <img src="images/dist/kaspi-gift-green.png" alt="" class="kaspi__item-img">
                        <div class="kaspi__item-about">
                            <div class="kaspi__item-title">
                                300 KZT
                            </div>
                            <div class="kaspi__item-text">
                                За каждую единицу сбора
                            </div>
                        </div>
                    </div>
                    <div class="kaspi__item">
                        <img src="images/dist/kaspi-gift-green.png" alt="" class="kaspi__item-img">
                        <div class="kaspi__item-about">
                            <div class="kaspi__item-title">
                                400 KZT
                            </div>
                            <div class="kaspi__item-text">
                                За каждую единицу сбора
                            </div>
                        </div>
                    </div>
                    <div class="kaspi__item">
                        <img src="images/dist/kaspi-gift.png" alt="" class="kaspi__item-img">
                        <div class="kaspi__item-about">
                            <div class="kaspi__item-title">
                                500 KZT
                            </div>
                            <div class="kaspi__item-text">
                                За каждую единицу сбора
                            </div>
                        </div>
                    </div>
                    <div class="kaspi__item">
                        <img src="images/dist/kaspi-gift.png" alt="" class="kaspi__item-img">
                        <div class="kaspi__item-about">
                            <div class="kaspi__item-title">
                                600 KZT
                            </div>
                            <div class="kaspi__item-text">
                                За каждую единицу сбора
                            </div>
                        </div>
                    </div>
                    <div class="kaspi__item">
                        <img src="images/dist/kaspi-gift.png" alt="" class="kaspi__item-img">
                        <div class="kaspi__item-about">
                            <div class="kaspi__item-title">
                                1000 KZT
                            </div>
                            <div class="kaspi__item-text">
                                За каждую единицу сбора
                            </div>
                        </div>
                    </div>
                    <div class="kaspi__item">
                        <img src="images/dist/kaspi-gift.png" alt="" class="kaspi__item-img">
                        <div class="kaspi__item-about">
                            <div class="kaspi__item-title">
                                1500 KZT
                            </div>
                            <div class="kaspi__item-text">
                                За каждую единицу сбора
                            </div>
                        </div>
                    </div>
                    <div class="kaspi__item">
                        <img src="images/dist/kaspi-gift.png" alt="" class="kaspi__item-img">
                        <div class="kaspi__item-about">
                            <div class="kaspi__item-title">
                                2000 KZT
                            </div>
                            <div class="kaspi__item-text">
                                За каждую единицу сбора
                            </div>
                        </div>
                    </div>
                    <div class="kaspi__item">
                        <img src="images/dist/kaspi-gift.png" alt="" class="kaspi__item-img">
                        <div class="kaspi__item-about">
                            <div class="kaspi__item-title">
                                3000 KZT
                            </div>
                            <div class="kaspi__item-text">
                                За каждую единицу сбора
                            </div>
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
    name: "PopupBonuses", 
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