<template>
<div class="popup__content  mt-5" :class="{'v-loading': loading}">

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
                            <th class="text-center">Дата</th>
                            <th class="text-center">Сумма</th>
                            <th class="text-center">Комментарии</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in history">
                            <td class="text-center">{{ (new Date(item.date)).addHours(6).toLocaleString('ru-RU') }}</td>
                            <td class="text-center">{{ item.sum }}</td>
                            <td>
                                <p class="fz14 mb-0" v-html="item.comment"></p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
          <div class="kaspi__content custom-scroll-y tab__content-item"  data-content="2">
            <!--                <div class="kaspi__wrapper">-->
            <!--                    -->
            <!--                    <div v-html="potential_bonuses"></div>-->

            <!--                </div>-->
            <table>
              <template v-for=" (bonus, i) in bonuses">
                <thead>

                <tr>
                  <div class="popup__subtitle">
                    {{ bonus.name }}

                  </div>

                </tr>
                <tr>
                  <th class="text-center">Название</th>
                  <th class="text-center">За что</th>
                  <th class="text-center">Сумма</th>
                </tr>
              </thead>
              <tbody>
              <tr v-for="item in bonus.items">
                <td class="text-center">{{ item.title }}</td>
                <td class="text-center">{{ item.text }}</td>
                <td class="text-center">{{ item.sum }}</td>

              </tr>
              </tbody>

              </template>

            </table>
          </div>


        </div>
    </div>
</div>
</template>

<script>
import {fields} from "../../kpi/bonuses";

export default {
    name: "PopupBonuses", 
    props: {},
    data: function () {
        return {
            fields: [],
            bonuses: [],
            currentMonth: null,
            dateInfo: {
                currentMonth: null,
                monthEnd: 0,
                workDays: 0,
                weekDays: 0,
                daysInMonth: 0
            },
            potential_bonuses: '',
            history: [],
            loading: false
        };
    },
    created(){
        this.setMonth()
        this.fetchData()
        this.fetchBonuses()
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
            this.loading = true

            axios
                .post("/bonuses", {
                    month: this.$moment(this.currentMonth, 'MMMM').format('M'),
                    year: new Date().getFullYear(),
                })
                .then((response) => {
                    // this.potential_bonuses = response.data.data.potential_bonuses
                    this.history = response.data.data.history

                    this.loading = false
                });
        },
      fetchBonuses(){
        this.loading = true
        const _this = this;
        axios
            .post("/bonus/user")
            .then((response) => {
             console.log(response);
             _this.bonuses = response.data.bonuses

              this.loading = false
            });
      }
    }
};
</script>