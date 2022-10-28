<template>
<div class="mb-0">

    <div class="row mb-3 mt-3" v-if="show_header">
        <div class="col-2">
            <select class="form-control" v-model="currentYear" @change="fetchData">
                <option v-for="year in years" :value="year" :key="year">{{ year }}</option>
            </select>
        </div>
        <div class="col-1">
            <div class="btn btn-primary rounded" @click="fetchData()">
                <i class="fa fa-redo-alt"></i>
            </div>
        </div>
        <div class="col-6">

        </div>
        <div class="col-3">
            
        </div>
    </div>


    <div class="table-resonsive  mt-3">
        <table class="table b-table table-sm  table-bordered" :key="ukey">
            <tr>
                <template v-for="(field, key) in fields">
                    <th :class="field.klass" style="background:#76c8ec">
                        <div>{{ field.name }}</div>
                    </th>
                </template>
            </tr>
            <tr v-for="(item, index) in users" :key="index">
                <template v-for="(field, key) in fields"> 
                    <td :class="field.klass">
                        <div class="inner">
                            <div>{{ item[field.key] }}</div>
                            

                            <div class="inner-text">
                                <b v-if="item.texts[field.key] !== undefined">Оценки ({{ item.grades[field.key] }})</b>
                                <div class="d-flex">
                                    <div class="w-50">
                                        <b>Плюсы ({{ item.texts[field.key] !== undefined ? item.texts[field.key].length : 0 }})</b>
                                        <div v-for="(text, index) in item.texts[field.key]">
                                            <b>{{ index + 1}}:</b> {{ text }}
                                        </div>
                                    </div>
                                    
                                    <div class="w-50">
                                        <b>Минусы ({{ item.minuses[field.key] !== undefined ? item.minuses[field.key].length : 0 }})</b>
                                        <div v-for="(text, index) in item.minuses[field.key]">
                                            <b>{{ index + 1}}:</b> {{ text }}
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </td>
                </template>
            </tr>

        </table>
    </div> 

 <div class="empty-space"></div>

</div>
</template>

<script>
export default {
    name: "NPS",
    props: {activeuserid: Number, show_header: {
        default: true
    }},
    data() {
        return {
            users: [],
            fields: [],
            years: [2020, 2021, 2022],
            currentYear: new Date().getFullYear(),
            monthInfo: {
                currentMonth: null,
                monthEnd: 0,
                workDays: 0,
                weekDays: 0,
                daysInMonth: 0
            },
            ukey: 1
        }
    },
    created() {
        this.setMonth();
        this.setMonthsTableFields();
        this.fetchData();
    },
    methods: {

        setMonth() {
            this.monthInfo.currentMonth = this.monthInfo.currentMonth ? this.monthInfo.currentMonth : this.$moment().format('MMMM')
            this.monthInfo.month = this.monthInfo.currentMonth ? this.$moment(this.monthInfo.currentMonth, 'MMMM').format('M') : this.$moment().format('M')
            let currentMonth = this.$moment(this.monthInfo.currentMonth, 'MMMM')
            //Расчет выходных дней
            this.monthInfo.monthEnd = currentMonth.endOf('month'); //Конец месяца
            this.monthInfo.weekDays = currentMonth.weekdayCalc(currentMonth.startOf('month').toString(), currentMonth.endOf('month').toString(), [6]) //Колличество выходных
            this.monthInfo.daysInMonth = new Date(2021, this.$moment(this.monthInfo.currentMonth, 'MMMM').format('M'), 0).getDate() //Колличество дней в месяце
            this.monthInfo.workDays = this.monthInfo.daysInMonth - this.monthInfo.weekDays //Колличество рабочих дней
        },

        fetchData() {
            let loader = this.$loading.show();

            axios.post('/timetracking/nps', {
                month: this.$moment(this.monthInfo.currentMonth, 'MMMM').format('M'),
                year: this.currentYear,
            }).then(response => {
                
                this.setMonth()
                this.users = response.data.users;
                this.ukey++;

                loader.hide()
            }).catch(error => {
                loader.hide()
                alert(error)
            });
        },

        setMonthsTableFields() {
            let fieldsArray = []
            let order = 1;
            
            fieldsArray.push({
                key: 'group_id',
                name: 'Отдел',
                order: order++,
                klass: ' text-left px-1 bg-blue w-200'
            }) 

            fieldsArray.push({
                key: 'position',
                name: 'Должность',
                order: order++,
                klass: ' text-left px-1 bg-blue'
            })  

            fieldsArray.push({
                key: 'name',
                name: 'ФИО',
                order: order++,
                klass: ' text-left px-1 bg-blue w-200'
            }) 
            

            for(let i = 1; i <= 12; i++) {

                if(i.length == 1) i = '0' + i

                fieldsArray.push({
                    key: i,
                    name: moment(this.currentYear + '-' + i + '-01').format('MMMM'),
                    order: order++,
                    klass: 'text-center px-1 month'
                })

            } 
            
            this.fields = fieldsArray    
        },
    }
};
</script>

<style scoped>
.month {
    width: 90px;
}
.month:hover div.inner {
    position: relative;
}
.month:hover div.inner {
    background: #eee;
    cursor: pointer;
}
div.inner-text {
    display: none;
}
.month:hover div.inner-text {
    position: absolute;
    top: 110%;
    left: -185px;
    padding: 15px;
    width: 400px;
    max-width: 400px;
    max-height: 300px;
    text-align: left;
    font-size: 11px;
    background: #fff7c8;
    border-radius: 5px;
    cursor: pointer;
    display: block;
    overflow-y:auto;
}
.bg-blue {
    background: aliceblue;
}
td.month {
    vertical-align: middle;
}
.w-200 {
    min-width: 200px;
}
.w-50 {
    width: 50%;
}
</style>
