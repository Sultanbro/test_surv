<template>
<div class="mt-3">
    <div class="mb-0">
        <div class="row mb-3">
            <div class="col-9">
                <h5>Начисления</h5>
            </div>
            <div class="col-3">
                <select class="form-control" v-model="dateInfo.currentMonth" @change="fetchData()">
                    <option v-for="month in $moment.months()" :value="month" :key="month">{{month}}</option>
                </select>
            </div>
        </div> 

        
        <b-table v-if="dataLoaded" responsive striped :sticky-header="true" class="text-nowrap text-right my-table mb-0" id="tabelTable" :small="true" :bordered="true" :items="items" :fields="fields" show-empty emptyText="Нет данных">
          <template #head(avanses)="data">

              <i class="fa fa-info-circle"
                 v-b-popover.hover.right.html="'Авансы'">
              </i>

          </template>
          <template #head(fines)="data">

              <i class="fa fa-info-circle"
                 v-b-popover.hover.right.html="'Депримирование'">
              </i>

          </template>
          <template slot="cell(avanses)" slot-scope="data">
              <div v-if="data.index == 1">
                {{data.value.value}}
              </div>
          </template>
          <template slot="cell(fines)" slot-scope="data" >
            <div v-if="data.index == 1">
              {{data.value.value}}
            </div>
          </template>
          <template slot="cell()" slot-scope="data">
                <div :class="{
                    'day-fine':data.value.hasFine,
                    'day-training':data.value.training,
                    'day-avans': data.value.hasAvans,
                    'day-bonus':data.value.hasBonus,
                }" @click="openDay(data.value)">
                    {{data.value.value}}
                </div>
            </template>
        </b-table>

    </div>
    <sidebar :title="sidebarTitle" :open="openSidebar" @close="openSidebar=false" v-if="openSidebar" width="350px">

        <h6 class="mt-3">Начислено</h6>
        <div  class="mb-5">
            <div v-if="Number(sidebarContent.value) > 0">{{ sidebarContent.calculated }}</div>
            <div v-else>0</div>
        </div>
        <div v-if="sidebarContent.training">
            <h6>Стажировка</h6>
            <p>Может быть пол суммы</p>
        </div>
        <h6 class="mt-3">Депримирование</h6>
        <div  class="mb-5">
            <template v-for="item in sidebarContent.fines">
                <p :key="item">{{item.name}}</p>
            </template>
            <p v-if="sidebarContent.fines.length == 0">
                Нет штрафов
            </p>
        </div>
        <h6 class="mt-3">Бонусы</h6>
        <div class="mb-5">
            <template v-for="item in sidebarContent.bonuses">
                <div :key="item">
                    <div>
                        <b>
                            {{ item.bonus }} KZT
                        </b>
                    </div>
                    <div>
                        {{ item.comment_bonus }}
                    </div>
                </div>
            </template>
            <div v-if="sidebarContent.bonuses.length == 0 && sidebarContent.awards.length == 0">
                Нет бонусов
            </div>
        </div>
        <div v-if="sidebarContent.awards.length != 0"  class="mb-5">
            <template v-for="item in sidebarContent.awards">
                <div :key="item">
                    <div>
                        <b>
                            {{ item.amount }} KZT
                        </b>
                    </div>
                    <div>
                        {{ item.comment }}
                    </div>
                </div>
            </template>
        </div>
        <h6 class="mt-3">Авансы</h6>
        <div  class="mb-5">
            <template v-for="item in sidebarContent.avanses">
                <div :key="item">
                    
                    <div>
                        <b>
                            {{ item.paid }} KZT
                        </b>
                    </div>
                    <div>
                        {{ item.comment_paid }}
                    </div>
                    
                </div>
            </template>
            <p v-if="sidebarContent.avanses.length == 0">
                Нет авансов
            </p>
        </div>
    </sidebar>
</div>
</template>

<script>

export default {
    name: "TableUserSalary",
    props: { 
        activeuserid: String
    },
    data() {
        return {
            data: {},
            openSidebar: false,
            sidebarContent: {},
            sidebarTitle: 'История',
            totalFines: 0,
            total_avanses: 0,
            items: {
                salaries: [],
                times: [],
                hours: []
            },
            fields: [],
            dayInfoText: '',
            hasPremission: false,
            dateInfo: {
                currentMonth: null,
                monthEnd: 0,
                workDays: 0,
                weekDays: 0,
                daysInMonth: 0
            },
            dataLoaded: false,
        }
    },

    created() {
        this.dateInfo.currentMonth = this.dateInfo.currentMonth ? this.dateInfo.currentMonth : this.$moment().format('MMMM')
        let currentMonth = this.$moment(this.dateInfo.currentMonth, 'MMMM')

        //Расчет выходных дней
        this.dateInfo.monthEnd = currentMonth.endOf('month'); //Конец месяца
        this.dateInfo.weekDays = currentMonth.weekdayCalc(this.dateInfo.monthEnd, [6]) //Колличество выходных
        this.dateInfo.daysInMonth = currentMonth.daysInMonth() //Колличество дней в месяце
        this.dateInfo.workDays = this.dateInfo.daysInMonth - this.dateInfo.weekDays //Колличество рабочих дней

        //Текущая группа

        this.fetchData()
    },
    methods: {
        //Установка выбранного месяца
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
        //Установка заголовока таблицы
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

        //Загрузка данных для таблицы
        fetchData() {
            let loader = this.$loading.show();

            axios.post('/timetracking/zarplata-table', {
                month: this.$moment(this.dateInfo.currentMonth, 'MMMM').format('M'),
            }).then(response => {

                this.data = response.data.data
                this.totalFines = response.data.totalFines
                this.total_avanses = response.data.total_avanses
                this.setMonth()
                this.setFields()
                this.loadItems()
                this.dataLoaded = true
                
                loader.hide()
            })
        },
        //Добавление загруженных данных в таблицу
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
                        'awards': this.data[key][keyt]['awards'],
                        'hasFine': this.data[key][keyt]['fines'] !== undefined && this.data[key][keyt]['fines'].length,
                        'hasBonus': (this.data[key][keyt]['bonuses'] !== undefined && this.data[key][keyt]['bonuses'].length) || (this.data[key][keyt]['awards'] !== undefined && this.data[key][keyt]['awards'].length),
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
        openDay(data) {
            this.openSidebar = true
            this.sidebarContent = {
                fines: data.fines,
                avanses: data.avanses,
                bonuses: data.bonuses,
                awards: data.awards,
                training: data.training,
            }
        }
    }
}
</script>

<style lang="scss">
.ui-sidebar__content {
    .custom-checkbox {
        margin-bottom: .7rem;
    }
}

.b-table-sticky-header {
    max-height: 450px;
}

.table-day-1 {
    color: rgb(0, 0, 0);
    background: rgb(204, 204, 204);
}

.table-day-2 {
    color: #fff;
    background: blue;
}

.table-day-3 {
    color: rgb(0, 0, 0);
    background: aqua;
}

.table-day-4 {
    color: rgb(0, 0, 0);
    background: rgb(200, 162, 200);
}

.table-day-5 {
    color: rgb(0, 0, 0);
    background: orange;
}




.my-table-max {
    max-height: inherit !important;

    .day {
        padding: 0 !important;
        text-align: center;

        &.Sat,
        &.Sun {
            background-color: #FEF2CB;
        }

        &.table-danger {
            background-color: #f5c6cb !important;
        }
    }

    tr:nth-child(8) {
        background: #ff33cc7a !important;
        color: #000 !important;

        td {
            background: #ff33cc7a !important;
            font-weight: bold;

            input {
                font-weight: bold;
            }
        }
    }

    tr:nth-child(15) {
        background: #ff33cc7a !important;
        color: #000 !important;

        td {
            background: #ff33cc7a !important;
            font-weight: bold;
        }
    }

}

.cell-input {
    background: none;
    border: none;
    text-align: center;
    -moz-appearance: textfield;
    font-size: .8rem;
    font-weight: normal;
    padding: 0;
    color: #000;
    border-radius: 0;

    &:focus {
        outline: none;
    }

    &::-webkit-outer-spin-button,
    &::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
}

$fine: red;
$avans: #28a761;
$bonus: #007bff;
$training: orange;

.fz-09 {
    font-size: 0.9rem;
}
.ui-sidebar__content {
    .custom-checkbox {
        margin-bottom: 0.7rem;
    }
}
.fine,.avans,.bonus {
    color:#fff;
}
.day-fine {
    background: $fine;
}
.day-training {
    background: $training;
    &.day-fine {background: linear-gradient(110deg, $training 50%, $fine 50%);}
}
.day-bonus {
    background: $bonus;
    &.day-fine {background: linear-gradient(110deg, $bonus 50%, $fine 50%);}
    &.day-training {background: linear-gradient(110deg, $bonus 50%, $training 50%);}
    &.day-fine.day-training {background: linear-gradient(110deg, $bonus 33%, transparent 33%), linear-gradient(110deg, $fine 66%, $training 66%);}
}
.day-avans {
    background:$avans;
    &.day-fine {background: linear-gradient(110deg, $avans 50%, $fine 50%);}
    &.day-bonus {background: linear-gradient(110deg, $avans 50%, $bonus 50%);}
    &.day-training {background: linear-gradient(110deg, $avans 50%, $training 50%);}
    &.day-bonus.day-fine {background: linear-gradient(110deg, $avans 33%, transparent 33%), linear-gradient(110deg, $bonus 66%, $fine 66%);}
    &.day-bonus.day-fine.day-training {background: linear-gradient(110deg, $avans 25%, transparent 25%), linear-gradient(110deg, $bonus 50%, $fine 50%), linear-gradient(110deg, $fine 75%, $training 75%);}
}


</style>
