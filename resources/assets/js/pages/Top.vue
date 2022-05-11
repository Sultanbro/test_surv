<template>
  <div class="mt-2">

    <div class="row mb-3 ">
      <div class="col-3">
        <select class="form-control" v-model="monthInfo.currentMonth" @change="fetchData">
          <option v-for="month in $moment.months()" :value="month" :key="month">{{ month }}</option>
        </select>
      </div>
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
        <group-premission :currentGroup="0" page="page-top"
                          v-if="activeuserid == 5 || activeuserid == 18"></group-premission>
      </div>
    </div>

    <!-- <a href="/timetracking/nps" class="btn link-btn" target="_blank">NPS</a> -->
    <a-tabs type="card" defaultActiveKey='1'>


      <a-tab-pane tab="Полезность" key="1">
        <div class="d-flex" style="margin-bottom: 350px">
          <top-gauges :utility_items="utility" :editable="true" wrapper_class="  br-1" :key="ukey" page="top"/>
        </div>
      </a-tab-pane>

      <a-tab-pane tab="Рентабельность операторов" key="2">

        <div class="d-flex flex-wrap mb-5" :key="ukey">
          <div v-for="(gauge, g_index) in rentability" :key="gauge.name">
            <div @click="gauge.editable = !gauge.editable">
              <v-gauge :value="gauge.value"
                       unit="%"

                       :options="gauge.options"
                       :maxValue="Number(gauge.max_value)"
                       :top="true"
                       height="75px"
                       width="125px"
                       gaugeValueClass="gauge-span"/>
            </div>

            <p class="text-center font-bold" style="font-size: 14px;margin-bottom: 0;">
              <a v-if="[42].includes(gauge.group_id)"
                 :href="'/timetracking/analytics?group='+ gauge.group_id + '&active=1&load=1'"
                 target="_blank">{{ gauge.name }}</a>
              <a v-else :href="'/timetracking/an?group='+ gauge.group_id + '&active=1&load=1'"
                 target="_blank">{{ gauge.name }}</a>
            </p>
            <p class="text-center font-bold text-14">{{ gauge.value }}%</p>


            <div v-if="gauge.editable" class="mb-5 edt-window" style="width: 125px;">
              <div>
                <div class="d-flex justify-content-between align-items-center">
                  <span class="pr-2 l-label">Max</span>
                  <input type="text" class="form-control form-control-sm w-250 wiwi" v-model="gauge.max_value">
                </div>
              </div>
              <div class="d-flex">
                <button @click="saveRentGauge(g_index)"
                        class="btn btn-primary btn-sm rounded mt-1 mr-2">Сохранить
                </button>
              </div>
            </div>

          </div>

        </div>


        <t-rentability :year="currentYear"></t-rentability>

      </a-tab-pane>


      <a-tab-pane tab="Выручка" key="3">
        <div class="table-responsive">
          <table class="table b-table table-striped table-bordered table-sm proceed no-table"
                 >
            <thead>
            <tr>
              <th v-for="(field, findex) in proceeds.fields" :key="findex"
                  class="t-name table-title"
                  :class="{
                                                'w-295': findex == 0,
                                                'w-125': findex == 1,
                                                'w-80': findex == 2,
                                                'w-60': findex == 3,
                                                'text-center': findex != 0,
                                                'text-left': findex == 0,
                                            }">


                <template v-if="['+/-'].includes(field)">
                  <i class="fa fa-info-circle"
                     v-b-popover.hover.right.html="'100% - ( План * Кол-во календарных дней )/ (Итого * Кол-во отработанных дней)'"
                     title="Опережение плана">
                  </i>
                </template>
                {{ field }}  <i class="fa fa-plus-square" v-if="field == 'Группа'" @click="addRow()"></i>
              </th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(record, rindex) in proceeds.records" :key="rindex">


              <td v-for="(field, findex) in proceeds.fields" :key="findex"
                  class="text-center t-name table-title" :class="{'bg-grey': ['w1', 'w2', 'w3', 'w4', 'w5', 'w6'].includes(field)}">

                <template v-if="!['План', 'Итого', '+/-', 'Группа'].includes(field)">
                  <div v-if="record['group_id'] < 0">
                    <input type="number"
                           class="input"
                           v-model="record[field]"
                           @change="updateProceed(record, field, 'day')">
                  </div>
                  <div v-else>
                    <span v-if="record[field] != 0">{{ record[field] }}</span>
                    <span v-else></span>
                  </div>
                </template>
                <template v-else>

                  <template v-if="field == 'Группа'">
                    <a :href="'/timetracking/an?group='+ record['group_id'] + '&active=1&load=1'"
                       target="_blank" v-if="record['group_id'] >= 0">
                       {{ record[field] }}
                    </a>
                    <div v-else>
                        <input type="text"
                              class="input-2"
                              v-model="record[field]"
                              @change="updateProceed(record, field, 'name')">
                    </div>
                  </template>
                  <template v-else>
                    {{ record[field] }}
                  </template>
                </template>

              </td>

            </tr>
            </tbody>
          </table>
        </div>


      </a-tab-pane>
      <a-tab-pane tab="" key="6">
      </a-tab-pane>
      <a-tab-pane tab="" key="7">
      </a-tab-pane>
      <a-tab-pane tab="" key="8">
      </a-tab-pane>

      <a-tab-pane tab="Прогноз" key="4">
        <table class="table b-table table-striped table-bordered table-sm w-700">
          <thead>
          <th class="text-left t-name table-title" style="background:#90d3ff">Группа

            <i class="fa fa-info-circle"
               v-b-popover.hover.right.html="'Прогноз по принятию сотрудников на месяц'"
               title="Группа">
            </i>

          </th>
          <th class="text-center t-name table-title">План

            <i class="fa fa-info-circle"
               v-b-popover.hover.right.html="'Общий план операторов на проект от Заказчика'"
               title="План">
            </i>
          </th>
          <th class="text-center t-name table-title">Факт

            <i class="fa fa-info-circle"
               v-b-popover.hover.right.html="'Фактически работают в группе на должности оператора'"
               title="Факт">
            </i>
          </th>
          <th class="text-center t-name table-title">Осталось принять</th>
          </thead>
          <tbody>
          <tr v-for="(group, index) in prognoz_groups">
            <td class="text-left t-name table-title align-middle" style="background:#90d3ff">{{ group.name }}</td>
            <td class="text-center t-name table-title align-middle">
              <input type="number" v-model="group.plan" @change="saveGroupPlan(index)">
            </td>
            <td class="text-center t-name table-title align-middle">{{ group.applied }}</td>
            <td class="text-center t-name table-title align-middle">
              {{ isNaN(group.left_to_apply) ? 0 : Number(group.left_to_apply) }}
            </td>
          </tr>
          </tbody>
        </table>
      </a-tab-pane>

      <a-tab-pane tab="NPS" key="5">
        <nps :activeuserid="activeuserid" :show_header="false"></nps>
      </a-tab-pane>


    </a-tabs>


    <div class="empty-space"></div>
  </div>


</template>

<script>
export default {
  name: "Top",
  props: ['data', 'activeuserid'],
  data() {
    return {
      rentability: [], // первая вкладка
      utility: [], // вторая
      proceeds: [], // третья
      prognoz_groups: [], //
      years: [2020, 2021, 2022],
      currentYear: new Date().getFullYear(),
      monthInfo: {
        currentMonth: null,
        monthEnd: 0,
        workDays: 0,
        weekDays: 0,
        daysInMonth: 0
      },
      gaugeOptions: {
        angle: 0,
        staticLabels: {
          font: "9px sans-serif", // Specifies font
          labels: [0, 50, 80, 100, 120], // Print labels at these values
          color: "#000000", // Optional: Label text color
          fractionDigits: 0, // Optional: Numerical precision. 0=round off.
        },
        staticZones: [
          {strokeStyle: "#F03E3E", min: 0, max: 49}, // Red
          {strokeStyle: "#fd7e14", min: 50, max: 79}, // Orange
          {strokeStyle: "#FFDD00", min: 80, max: 90}, // Yellow
          {strokeStyle: "#30B32D", min: 91, max: 120}, // Green
        ],
        pointer: {
          length: 0.5, // // Relative to gauge radius
          strokeWidth: 0.025, // The thickness
          color: "#000000", // Fill color
        },
        limitMax: true,
        limitMin: true,
        lineWidth: 0.2,
        radiusScale: 0.8,
        colorStart: "#6FADCF",
        generateGradient: true,
        highDpiSupport: true,
      },
      ukey: 1
    }
  },
  created() {
    this.rentability = this.data.rentability;
    this.utility = this.data.utility;
    this.proceeds = this.data.proceeds;
    this.prognoz_groups = this.data.prognoz_groups
    this.setMonth()
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

      axios.post('/timetracking/top', {
        month: this.$moment(this.monthInfo.currentMonth, 'MMMM').format('M'),
        year: this.currentYear,
      }).then(response => {

        this.setMonth()

        this.rentability = response.data.rentability;
        this.utility = response.data.utility;
        this.proceeds = response.data.proceeds;

        this.ukey++;
        console.log(response.data);

        loader.hide()
      }).catch(error => {
        loader.hide()
        alert(error)
      });
    },

    saveRentGauge(g_index) {
      let loader = this.$loading.show();
      axios.post('/timetracking/top/save_rent_max', {
        gauge: this.rentability[g_index]
      })
          .then(response => {
            this.$message.success('Успешно сохранено!')
            this.rentability[g_index].editable = false
            this.fetchData()
            loader.hide()
          }).catch(error => {
        alert(error)
        loader.hide()
      });
    },

    saveGroupPlan(index) {
      let loader = this.$loading.show();
      axios.post('/timetracking/top/save_group_plan', {
        group_id: this.prognoz_groups[index].id,
        plan: this.prognoz_groups[index].plan,
      })
          .then(response => {
            this.$message.success('Успешно сохранено!')
            this.prognoz_groups[index].left_to_apply = Number(this.prognoz_groups[index].plan) - Number(this.prognoz_groups[index].fired);
            loader.hide()
          }).catch(error => {
        alert(error)
        loader.hide()
      });
    },

    updateProceed(record, field, type) {
      let loader = this.$loading.show();

      axios.post('/timetracking/top/proceeds/update', {
        group_id: record['group_id'],
        value: record[field],
        date: field == 'Группа' ?  this.proceeds.fields[5] : field,
        name: record['Группа'],
        type: type,
        year: this.currentYear,
      })
          .then(response => {
            this.$message.success('Успешно сохранено!');
            loader.hide()
          }).catch(error => {
        alert(error)
        loader.hide()
      });
    },

    addRow() {
      let length = this.proceeds.records.length;
    let obj = {};
      this.proceeds.fields.forEach(field => {
        obj[field] = null;
      });
      
      obj['group_id'] = this.proceeds.lowest_id - 1;

      this.proceeds.records.splice(length - 1, 0, obj);
    }

  }
}
</script>

<style lang="scss">
.gauge-title {
  font-weight: bold;
  display: none;
  text-align: center;
  font-size: 20px;
}

.w-250 {
  width: 200px;
}

.w-300 {
  width: 220px;
}

.w-full {
  width: 100%;
}

.w-295 {
  width: 295px;
  min-width: 295px !important;
}

.w-80 {
  width: 80px;
  min-width: 80px !important;
}

.w-125 {
  width: 125px;
  min-width: 125px !important;
}

.w-60 {
  width: 60px;
  min-width: 60px !important;
}
.bg-grey {
    background: #f0f0f0;
}
.fa-cog {
  display: none;
  font-size: 12px;
  position: relative;
  top: -2px;
  color: #1076b0;
  cursor: pointer;
}

.gauge:hover .fa-cog {
  display: block;
}

.gauge {
  cursor: pointer;


  &:last-child {
    border-bottom: none;
  }
}

.br-1 {
  border-right: 1px solid #f3f3f3;
  border-bottom: 1px solid #f3f3f3;
}

.text-20 {
  font-size: 20px;
}

table th:first-child,
table td:first-child {
  background: #f0f0f0;
  font-weight: bold;
  min-width: 200px;
  text-align: left !important;
  position: sticky;
  left: 0;
  border-right: 2px solid #a1b7cc !important;
  border-left: 2px solid #a1b7cc !important;
}

table.proceed tr:last-child td {
  font-weight: 700;
  color: #045e92;
}

input.form-control.form-control-sm.wiwi {
  padding: 0 10px;
  margin-bottom: 4px;
  width: 100%;
}

.link-btn {
  border-radius: 3px;
  text-align: center;
  cursor: pointer;
  position: absolute;
  right: 5px;
  top: 76px;
}

.w-700 {
  width: 700px;
}

.w-700 input {
  border: 0;
  text-align: center;
  width: 43px;
}
.input {
    border: 0;
    text-align: center;
    margin-bottom: 0;
    padding-left: 19px;
    width: 100px;
    background: transparent;
}
.input-2 {
      text-align: left;
    width: 100%;
    border: 0;
    margin-bottom: 0;
    background: transparent;

}
.no-table {
    width: auto !important;
}
</style> 
