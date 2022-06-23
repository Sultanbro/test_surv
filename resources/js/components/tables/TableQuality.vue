<template>
  <div class="mt-2 px-3 quality quality-page">
    <div class="row">

      <div class="col-3" v-if="individual_request">
        <select class="form-control" v-model="currentGroup" @change="fetchData">
          <option v-for="group in groups" :value="group.id" :key="group.id">
            {{ group.name }}
          </option>
        </select>
      </div>
      <div class="col-2">
        <select
          class="form-control"
          v-model="monthInfo.currentMonth"
          @change="fetchData"
        >
          <option v-for="month in $moment.months()" :value="month" :key="month">
            {{ month }}
          </option>
        </select>
      </div>
      <div class="col-2">
        <select class="form-control" v-model="currentYear" @change="fetchData">
          <option v-for="year in years" :value="year" :key="year">
            {{ year }}
          </option>
        </select>
      </div>
      <div class="col-3 d-flex align-items-start">
        <div class="btn btn-primary rounded mr-2" @click="fetchData()">
          <i class="fa fa-redo-alt"></i>
        </div>
      </div>
      <div
        class="col-2"
        v-if="
          Number(activeuserid) == 18 ||
          Number(activeuserid) == 5
        "
      >
        <button class="btn btn-primary d-block ml-auto" @click="showSettings = true">
          <i class="fa fa-cogs mr-2"></i>
          Настройки
        </button>
      </div>
    </div>



    <h4 class="d-flex align-items-center">
      <div class="mr-2 mt-2">{{ groupName }}</div>
    </h4>
    <div v-if="this.hasPermission">
      <a-tabs type="card" :defaultActiveKey="active">
        <a-tab-pane tab="Оценка диалогов" :key="1">
          <a-tabs type="card" v-if="dataLoaded" >
            <a-tab-pane tab="Неделя" :key="1">
              <div class="table-responsive my-table">
                <table class="table b-table table-bordered table-sm">
                  <tr>
                    <th class="b-table-sticky-column text-left t-name wd">
                      <div>Сотрудник</div>
                    </th>
                    <template v-for="(field, key) in fields">
                      <th :class="field.klass">
                        <div>{{ field.name }}</div>
                      </th>
                    </template>
                  </tr>

                  <tr v-for="(item, index) in items" :key="index">
                    <td class="b-table-sticky-column text-left t-name wd">
                      <div>
                        {{ item.name }}
                        <b-badge
                          variant="success"
                          v-if="item.groupName == 'Просрочники'"
                          >{{ item.groupName }}</b-badge
                        >
                        <b-badge variant="primary" v-else>{{
                          item.groupName
                        }}</b-badge>
                      </div>
                    </td>
                    <template v-for="(field, key) in fields">
                      <td :class="field.klass" :key="key">
                        <input
                          v-if="field.type == 'day' && can_add_records != true"
                          type="number"
                          :title="field.key + ' :' + item.name"
                          class="form-control cell-input"
                          @change="updateWeekValue(item, field.key)"
                          v-model="item.weeks[field.key]"
                        />
                        <div v-else>
                          <div v-if="item.weeks[field.key] != 0">
                            {{ item.weeks[field.key] }}
                          </div>
                        </div>
                      </td>
                    </template>
                  </tr>
                </table>
              </div>
            </a-tab-pane>
            <a-tab-pane tab="Месяц" :key="2">
              <div class="table-responsive my-table">
                <table class="table b-table table-sm table-bordered">
                  <tr>
                    <th class="b-table-sticky-column text-left t-name wd">
                      <div>Сотрудник</div>
                    </th>
                    <template v-for="(field, key) in monthFields">
                      <th :class="field.klass">
                        <div>{{ field.name }}</div>
                      </th>
                    </template>
                  </tr>
                  <tr v-for="(item, index) in items" :key="index">
                    <td class="b-table-sticky-column text-left t-name wd">
                      <div>
                        {{ item.name }}
                        <b-badge
                          variant="success"
                          v-if="item.groupName == 'Просрочники'"
                          >{{ item.groupName }}</b-badge
                        >
                        <b-badge variant="primary" v-else>{{
                          item.groupName
                        }}</b-badge>
                      </div>
                    </td>

                    <template v-for="(field, key) in monthFields">
                      <td :class="field.klass">
                        <div>{{ item.months[field.key] }}</div>
                      </td>
                    </template>
                  </tr>
                </table>
              </div>
            </a-tab-pane>
            <a-tab-pane
              tab="Оценка переговоров"
              key="3"
              @change="changeTab"
              v-if="can_add_records"
            >
              <div class="row">
                <div class="col-6 col-md-3">
                  <select
                    class="form-control"
                    v-model="filters.currentEmployee"
                    @change="filterRecords"
                  >
                    <option :value="0">Выберите сотрудника</option>
                    <option
                      v-for="item in items"
                      :value="item.id"
                      :key="item.id"
                    >
                      {{ item.name }}
                    </option>
                  </select>
                </div>
                <div class="col-2 col-md-1 d-flex align-items-center">
                  <select
                    class="form-control"
                    v-model="currentDay"
                    @change="fetchData"
                  >
                    <option value="0">Все дни</option>
                    <option
                      v-for="day in this.monthInfo.daysInMonth"
                      :value="day"
                      :key="day"
                    >
                      {{ day }}
                    </option>
                  </select>
                </div>
                <div class="col-4 col-md-8 d-flex align-items-center">
                  <b-button variant="primary" @click="addRecord" class="mr-1">
                    <i class="fa fa-plus"></i> Добавить запись
                  </b-button>
                  <b-button
                    variant="success"
                    @click="exportData()"
                    class="mr-1"
                  >
                    <i class="far fa-file-excel"></i> 20
                  </b-button>
                  <b-button variant="success" @click="exportAll()">
                    <i class="far fa-file-excel"></i> Экспорт
                  </b-button>
                </div>
                <div class="col-12 col-md-12 d-flex mt-2 mb-2">
                  <p class="mb-0">
                    Найдено записей: <b class="bluish">{{ records.total }}</b>
                  </p>
                  <p class="ml-3 mb-0" v-if="records_unique != 0">
                    Кол-во сотрудников:
                    <b class="bluish">{{ records_unique }}</b>
                  </p>
                  <p class="ml-3 mb-0" v-if="avgMonth != 0">
                    Среднее за месяц: <b class="bluish">{{ avgMonth }}</b>
                  </p>
                  <p class="ml-3 mb-0" v-if="avgDay != 0">
                    Среднее за день: <b class="bluish">{{ avgDay }}</b>
                  </p>
                </div>
              </div>

              <div class="table-responsive my-table">
                <table
                  class="table b-table table-sm table-bordered records-table"
                >
                  <tr>
                    <th class="b-table-sticky-column text-left t-name wd">
                      <div>Сотрудник</div>
                    </th>
                    <template v-for="(field, key) in recordFields">
                      <th :class="field.klass">
                        <div>{{ field.name }}</div>
                      </th>
                    </template>
                    <th class="actions"></th>
                    <th class="actions"></th>
                  </tr>

                  <!-- RECORDS -->
                  <template v-for="(record, index) in records.data">
                    <tr
                      :class="{
                        selected: record.editable,
                        changed: record.changed,
                      }"
                    >
                      <td class="b-table-sticky-column text-left t-name wd">
                        <div @click="editMode(record)">
                          {{ record["name"] }}
                        </div>
                      </td>

                      <template v-if="currentGroup == 42">
                        <td
                          class="text-left segment-width"
                          v-if="record.editable"
                        >
                          <div>
                            <select
                              v-model="record.segment_id"
                              class="form-control text-center sg"
                              @change="statusChanged(record)"
                            >
                              <option
                                :value="index"
                                v-for="(segm, index) in segment"
                              >
                                {{ segm }}
                              </option>
                            </select>
                          </div>
                        </td>
                        <td
                          v-else
                          class="text-center segment-width"
                          @click="editMode(record)"
                        >
                          <div>
                            {{ segment[record.segment_id] }}
                          </div>
                        </td>
                      </template>

                      <td class="text-center phoner" v-if="record.editable">
                        <div>
                          <input
                            type="text"
                            v-model="record.phone"
                            class="form-control text-center"
                            @focus="$event.target.select()"
                            @change="statusChanged(record)"
                          />
                        </div>
                      </td>
                      <td
                        class="text-center phoner"
                        v-else
                        @click="editMode(record)"
                      >
                        <div>
                          {{ record.phone }}
                        </div>
                      </td>

                      <template v-if="currentGroup == 42">
                        <td class="text-center" v-if="record.editable">
                          <div>
                            <input
                              type="text"
                              v-model="record.dayOfDelay"
                              class="form-control text-center"
                              @focus="$event.target.select()"
                              @change="statusChanged(record)"
                            />
                          </div>
                        </td>
                        <td
                          class="text-center"
                          v-else
                          @click="editMode(record)"
                        >
                          <div>
                            {{ record.dayOfDelay }}
                          </div>
                        </td>
                      </template>

                      <td class="text-center" v-if="record.editable">
                        <div>
                          <input
                            type="text"
                            v-model="record.interlocutor"
                            class="form-control text-center"
                            @focus="$event.target.select()"
                            @change="statusChanged(record)"
                          />
                        </div>
                      </td>
                      <td class="text-center" v-else @click="editMode(record)">
                        <div>
                          {{ record.interlocutor }}
                        </div>
                      </td>

                      <td class="text-center" v-if="record.editable">
                        <div>
                          <input
                            type="date"
                            v-model="record.date"
                            class="form-control text-center"
                            placeholder="dd-mm-yyyy"
                            min="1997-01-01"
                            max="2030-12-31"
                            @change="statusChanged(record)"
                          />
                        </div>
                      </td>
                      <td class="text-center" v-else @click="editMode(record)">
                        <div>
                          {{ record.date }}
                        </div>
                      </td>

                      <template v-for="(param, pk) in params">
                        <td class="text-center params" v-if="record.editable">
                          <div>
                            <input
                              type="number"
                              v-model="record['param' + pk]"
                              class="form-control text-center"
                              @change="changeStat(record)"
                              @focus="$event.target.select()"
                            />
                          </div>
                        </td>
                        <td
                          class="text-center params"
                          v-else
                          @click="editMode(record)"
                        >
                          <div>
                            {{ record["param" + pk] }}
                          </div>
                        </td>
                      </template>

                      <td class="text-center">
                        <div>
                          {{ record.total }}
                        </div>
                      </td>

                      <td class="text-left" v-if="record.editable">
                        <div>
                          <input
                            type="text"
                            v-model="record.comments"
                            class="form-control"
                            @focus="$event.target.select()"
                            @change="statusChanged(record)"
                          />
                        </div>
                      </td>
                      <td class="text-left" v-else @click="editMode(record)">
                        <div class="pl2">
                          {{ record.comments }}
                        </div>
                      </td>

                      <td class="actions" @click="editMode(record)">
                        <div>
                          <b-button
                            v-if="record.editable"
                            variant="success"
                            size="sm"
                            @click="saveRecord(record)"
                          >
                            <i class="fa fa-save"></i>
                          </b-button>
                        </div>
                      </td>
                      <td class="actions" @click="editMode(record)">
                        <div>
                          <b-button
                            variant="danger"
                            size="sm"
                            @click="deleteRecordModal(record, index)"
                          >
                            <i class="fa fa-trash"></i>
                          </b-button>
                        </div>
                      </td>
                    </tr>
                  </template>
                </table>
              </div>
              <div>
                <pagination
                  :data="records"
                  @pagination-change-page="getResults"
                  :limit="3"
                ></pagination>
              </div>
            </a-tab-pane>
          </a-tabs>
        </a-tab-pane>
        <a-tab-pane tab="Прогресс по курсам" :key="2">

            <div class="row course-progress mb-3">
              <div class="col-3">
                <div class="box">
                  <div class="item d-flex">
                    <p>Сотрудников в обучении на месяц</p>
                    <vue-circle
                      :progress="57"
                      :size="100"
                      :reverse="false"
                      line-cap="round"
                      :fill="fill"
                      empty-fill="rgba(0, 0, 0, .1)"
                      :animation-start-value="0.0"
                      :start-angle="1"
                      insert-mode="append"
                      :thickness="8"
                      :show-percent="true">
                        <p>5 из 290</p>
                    </vue-circle>
                  </div>
                </div>
              </div>
              <div class="col-9">
                <div class="box">
                  <div class="item">
                    <p>За неделю</p>
                    <vue-circle
                      :progress="23"
                      :size="100"
                      :reverse="false"
                      line-cap="round"
                      :fill="fill"
                      empty-fill="rgba(0, 0, 0, .1)"
                      :animation-start-value="0.0"
                      :start-angle="1"
                      insert-mode="append"
                      :thickness="8"
                      :show-percent="true">
                        <p>5 из 290</p>
                    </vue-circle>
                  </div>

                  <div class="item">
                    <p>За сегодня</p>
                    <vue-circle
                      :progress="15"
                      :size="100"
                      :reverse="false"
                      line-cap="round"
                      :fill="fill"
                      empty-fill="rgba(0, 0, 0, .1)"
                      :animation-start-value="0.0"
                      :start-angle="1"
                      insert-mode="append"
                      :thickness="8"
                      :show-percent="true">
                        <p>5 из 290</p>
                    </vue-circle>
                  </div>
                  <div class="item">
                    <p>За вчера</p>
                    <vue-circle
                      :progress="89"
                      :size="100"
                      :reverse="false"
                      line-cap="round"
                      :fill="fill"
                      empty-fill="rgba(0, 0, 0, .1)"
                      :animation-start-value="0.0"
                      :start-angle="1"
                      insert-mode="append"
                      :thickness="8"
                      :show-percent="true">
                        <p>5 из 290</p>
                    </vue-circle>
                  </div>
                </div>
              </div>
            

            </div>
            <course-results  :monthInfo="monthInfo" :currentGroup="currentGroup" />

        </a-tab-pane>

        <a-tab-pane tab="Чек Лист" :key="3" type="card"    >


          <div class="col-md-12 p-0">
            <div class="col-md-6 p-0">
              <div>
                 <button @click="viewStaticCheck('w')" type="button" class="btn btn-light p-2 pl-4 pr-4" style="background-color: white;color: rgb(24 144 255);border: 1px solid #e8e8e8">Неделя</button>
                 <button @click="viewStaticCheck('m')" type="button" class="btn btn-light p-2 pl-4 pr-4" style="color: #999999;border: 1px solid #e8e8e8">Месяц</button>
              </div>
            </div>

            <div v-if="viewStaticButton.weekCheck" class="table-responsive my-table">
              <table class="table b-table table-bordered table-sm">
                <tr>
                  <th class="b-table-sticky-column text-left t-name wd">
                    <div>Сотрудник</div>
                  </th>
                  <template v-for="(field, key) in fields">
                    <th >

                      <div>{{ field.name }}</div>
                    </th>
                  </template>
                </tr>
               <template v-for="( check_r,index ) in check_result">
                 <tr :key="index">
                   <th class="b-table-sticky-column text-left t-name wd">
                     {{ check_r.last_name }} {{ check_r.name }}
                   </th>
                   <template v-for="(field, key) in fields">



                     <td :class="field.klass" :key="key">
                       <template v-if="currentGroup == check_r.gr_id" >

                         <div v-if="field.name == 'Итог' ">
                           {{check_r.total_day}}
                         </div>

                         <template v-for="(checked_day,index) in check_r.day">



                           <template v-if="index == field.name">
                             {{checked_day}}
                           </template>



                         </template>
                       </template>
                     </td>
                   </template>
                 </tr>
               </template>
              </table>
            </div>

            <div v-if="viewStaticButton.montheCheck" class="table-responsive my-table mt-5">
              <table class="table b-table table-sm table-bordered">
                <tr>
                  <th class="b-table-sticky-column text-left t-name wd">
                    <div>Сотрудник</div>
                  </th>
                  <template v-for="(field, key) in monthFields">
                    <th :class="field.klass">
                      <div>{{ field.name }}</div>
                    </th>
                  </template>
                </tr>
                <template v-for="( check_r,index ) in check_result">
                  <tr :key="index">
                    <th class="b-table-sticky-column text-left t-name wd">
                      {{ check_r.name }}
                    </th>
                    <template v-for="(field, key) in monthFields">
                      <td :class="field.klass" :key="key">

                        <template v-if="currentGroup == check_r.gr_id" >

                          <div v-if="field.name == 'Итог' ">
                            {{check_r.total_month}}
                          </div>
                          <template v-for="(checked_m,index) in check_r.month">
                            <template v-if="index == field.key">
                              {{checked_m}}
                            </template>

                          </template>
                        </template>




                      </td>
                    </template>
                  </tr>
                </template>
              </table>
            </div>
          </div>

        </a-tab-pane>
      </a-tabs>
    </div>
    <div v-else>
      <p>У вас нет доступа к этой группе</p>
    </div>

    <b-modal id="delete-modal" hide-footer>
      <template #modal-title> Подтвердите удаление </template>
      <div class="">
        <div class="row">
          <div class="col-md-12">
            <div>Вы собираетесь удалить следующую запись</div>
            <div>{{ newRecord }}</div>
          </div>
        </div>
        <div class="d-flex">
          <b-button
            class="mt-3 mr-1"
            variant="danger"
            block
            @click="deleteRecord"
            >Удалить</b-button
          >
          <b-button
            variant="primary"
            class="mt-3 ml-1"
            block
            @click="$bvModal.hide('delete-modal')"
            >Отмена</b-button
          >
        </div>
      </div>
    </b-modal>



    <b-modal
      v-model="showSettings"
      title="Настройки"
      :width="400"
      hide-footer
    >
      <div class="row">
        <div class="col-12 d-flex mb-3">

          <div class="fl">Источник оценок
            <i class="fa fa-info-circle ml-2" 
                v-b-popover.hover.right.html="'Заполнять оценки диалогов и критерии на странице <b>Контроль качества</b>, либо подтягивать их по крону с cp.callibro.org'" 
                title="Оценки контроля качества">
            </i>
          </div>
          <div class="fl d-flex ml-3">
            <b-form-radio v-model="can_add_records"  name="some-radios" :value="false" class="mr-3">C U-calls</b-form-radio>
            <b-form-radio v-model="can_add_records"  name="some-radios" :value="true">Ручная оценка</b-form-radio>
          </div>

        </div>

        <div class="col-12" v-if="!can_add_records">
           <div class="bg mb-2">
            <div class="fl">ID диалера 
              <i class="fa fa-info-circle ml-2" 
                  v-b-popover.hover.right.html="'Нужен, чтобы <b>подтягивать часы</b> или <b>оценки диалогов</b> для контроля качества.<br>С сервиса cp.callibro.org'" 
                  title="Диалер в U-Calls">
              </i>
            </div>
            <div class="fl d-flex mt-1">
              <input type="text" v-model="dialer_id" placeholder="ID" class="form-control form-control-sm" />
              <input type="number" v-model="script_id" placeholder="ID скрипта" class="form-control form-control-sm" />
            </div>
          </div>
        </div>

        <div class="col-12" v-if="can_add_records">
           <div class="row">
              <div class="col-12 d-flex mb-1" v-for="crit in params">
                <b-form-checkbox
                  v-model="crit.active"
                  :value="1"
                  :unchecked-value="0"
                >
                </b-form-checkbox>
                <input
                  type="text"
                  v-model="crit.name"
                  class="form-control form-control-sm"
                />
              </div>

              <div class="col-12">
                <button class="btn btn-sm btn-default rounded" style="font-size:12px;" @click="addParam()">
                  Добавить критерий
                </button>
              </div>
            </div>
        </div>
             
        <div class="col-12 mt-3">
          <button class="btn btn-sm btn-primary rounded" @click="saveSettings">
            Сохранить 
          </button>
        </div>
      </div>
    </b-modal>
  </div>
</template>

<script>
export default {
  name: "TableQuality",
  props: {
    activeuserid: String,
    groups: Array,
    individual_type:{
      default:null
    },
    individual_type_id:{
      default:null
    }


  },
  data() {
    return {
      fields: [],
      monthFields: [],
      recordFields: [],
      filters: {
        currentEmployee: 0,
        fromDate: moment().format("YYYY-MM-DD"),
        toDate: moment().format("YYYY-MM-DD"),
      },
      can_add_records: false, // like kaspi
      script_id: null,
      dialer_id: null,
      fieldsNumber: 15,
      pageNumber: 1,
      currentDay: new Date().getDate(),
      avgDay: 0,
      avgMonth: 0,
      showCritWindow: false,
      showSettings: false,
      newRecord: {
        id: 0,
        employee_id: 0,
        name: "",
        segment: "1-5",
        segment_id: 1,
        interlocutor: "Клиент",
        phone: "",
        dayOfDelay: moment().format("YYYY-MM-DD"),
        date: moment().format("YYYY-MM-DD"),
        param1: 0,
        param2: 0,
        param3: 100,
        param4: 0,
        param5: 0,
        comments: "",
        changed: true,
      },
      records_unique: 0,
      records: {
        data: [],
      },
      deletingElementIndex: 0,
      currentGroup: 42,
      groupName: "Контроль качества",
      monthInfo: {},
      user_ids: {},
      years: [2020, 2021, 2022],
      currentYear: new Date().getFullYear(),
      hasPermission: false,
      dataLoaded: true,
      segment: {
        1: "1-5",
        2: "Нап",
        3: "3160",
        4: "6190",
        5: "ОВД",
        6: "1-5 RED",
        7: "Нап RED",
        10: "ОВД RED",
        11: "6_30 RED",
        12: "6_30",
      },
      message: null,
      loader: null, 
      fill:{ gradient: ["#1890ff", "#28a745"] },
      items: [],
      params: [],
      pagination: {
        current_page: 1,
        first_page_url: "",
        from: 1,
        last_page: 1,
        last_page_url: "",
        next_page_url: "",
        per_page: 100,
        prev_page_url: null,
        to: 100,
        total: 4866,
      },
      individual_request:true,

      viewStaticButton:{
          weekCheck:true,
          montheCheck:false
      },
      active:1,
      selected_active:1,

    };
  },

  created() {


    if (this.individual_type != null  &&  this.individual_type_id != null){

      this.active = 3;


    }


    this.fetchData();


  },
  methods: {



    viewStaticCheck(type){
        // console.log(this.fields,'day');
        // console.log(this.monthFields,'mont');
        // console.log(this.currentGroup,'щзешщт')
        console.log(this.fields,'fields')
        console.log(this.check_result,'result');
        // console.log(this.items,'items')
        if (type == 'w'){
            this.viewStaticButton.weekCheck = true
            this.viewStaticButton.montheCheck = false
        }else if(type == 'm'){
            this.viewStaticButton.weekCheck = false
            this.viewStaticButton.montheCheck = true
        }

      }  ,

    watchChanges(values, oldValues) {
      const index = values.findIndex(function (v, i) {
        return v !== oldValues[i];
      });
      console.log(this.records.data[index]);
      this.records.data[index].changed = true;
    },

    getResults(page = 1) {
      this.fetchItems("/timetracking/quality-control/records?page=" + page);
    },

    fetchData() {
      let loader = this.$loading.show();

      this.setDates();

      this.fetchItems();

      loader.hide();
    },

    normalizeItems() {

      // console.log(this.items)
      // console.log(this.items.length)

      if (this.items.length > 0) {
        this.newRecord.employee_id = this.items[0].id;
        this.newRecord.name = this.items[0].name;
      }

      this.records.data.forEach((record, index) => {
        record.segment = this.segment[record.segment_id];
        record.changed = false;

        this.params.forEach((param, key) => {
          record["param" + key] = 0;
        });

        record.param_values.forEach((item, key) => {
          this.params.forEach((param, key) => {
            if (item.param_id == param.id) {
              record["param" + key] = item.value;
            }
          });
        });
      });
    },

    addParam() {
      this.params.push({
        name: "Новый критерий",
        id: -1,
        active: 0,
      });
    },

    saveSettings() {
      let loader = this.$loading.show();

      // if (this.individual_type != null  &&  this.individual_type_id != null) {
      //
      // }

      axios
        .post("/timetracking/quality-control/crits/save", {
          crits: this.params,
          can_add_records: this.can_add_records,
          script_id: this.script_id,
          dialer_id: this.dialer_id,
          group_id: this.currentGroup,


        })
        .then((response) => {
          console.log(response);
          this.$message.success("Сохранено!!");
          this.showSettings = false;
          this.fetchData();
          loader.hide();
        })
        .catch(function (e) {
          loader.hide();
          alert(e);
        });
    },

    fetchItems($url = "/timetracking/quality-control/records") {
      let loader = this.$loading.show();

      // selected_active
      // console.log(this.active,'ssssssssssss')

      if (this.active == 3 && this.individual_type == 2){
          this.currentGroup = this.individual_type_id
      }

      axios
        .post($url, {
          day: this.currentDay,
          month: this.monthInfo.month,
          year: this.currentYear,
          employee_id: this.filters.currentEmployee,
          group_id: this.currentGroup,
          individual_type:this.individual_type,
          individual_type_id:this.individual_type_id,
        })
        .then((response) => {

          console.log(response,'response');
          // console.log(response.data['individual_type'],'ind');
          // console.log(this.fields,'fields');

          // if (response.data['individual_type'] == 2 || response.data['individual_type'] == 3){
          //   this.individual_request = false
          // }else {
          //   this.individual_request = true
          // }


          this.currentGroup = response.data['individual_current']


          if (response.data.error && response.data.error == "access") {
            console.log(response,'responseError');
            this.hasPermission = false;
            loader.hide();
            return;
          }





          this.check_result = response.data.check_users;

          this.hasPermission = true;
          this.items = response.data.items;
          this.records = response.data.records;
          this.records_unique = response.data.records_unique;
          this.avgDay = response.data.avg_day;
          this.avgMonth = response.data.avg_month;
          this.records = response.data.records;
          this.can_add_records = response.data.can_add_records;
          this.params = response.data.params;
          this.script_id = response.data.script_id;
          this.dialer_id = response.data.dialer_id;



          this.$message.success("Записи загружены");
          this.normalizeItems();
          this.createUserIdList();
          this.setWeeksTable();
          this.setMonthsTable();

          this.setRecordsTable();
          this.calcTotalWeekField();

          loader.hide();
        });
    },

    chooseEmployee(record) {
      var name = this.items.filter((item) => {
        return record.employee_id == item.id;
      });
      record["name"] = name[0]["name"];
    },

    setDates() {
      this.setYear();
      this.setMonth();
    },

    filterRecords() {
      this.fetchItems();
    },
    setWeeksTable() {
      this.setWeeksTableFields();
    },

    setMonthsTable() {
      this.setMonthsTableFields();
    },

    statusChanged(record) {
      record.changed = true;
    },

    createUserIdList() {
      this.items.forEach((item, index) => {
        this.user_ids[item.id] = item.name;
      });
    },

    editRecordModal(record) {
      this.newRecord.id = record.id;
      this.newRecord.name = record.name;
      this.newRecord.interlocutor = record.interlocutor;
      this.newRecord.employee_id = record.employee_id;
      this.newRecord.phone = record.phone;
      this.newRecord.dayOfDelay = record.dayOfDelay;
      this.newRecord.date = record.date;
      this.newRecord.param1 = record.param1;
      this.newRecord.param2 = record.param2;
      this.newRecord.param3 = record.param3;
      this.newRecord.param4 = record.param4;
      this.newRecord.param5 = record.param5;
      this.newRecord.total = record.total;
      this.newRecord.comments = record.comments;
      this.$bvModal.show("bv-modal");
    },

    addRecord() {
      if (this.filters.currentEmployee == 0)
        return this.$message.info("Выберите сотрудника!");

      if (this.records.data.length != 0) this.records.data[0].editable = false;

      let obj = {
        id: 0,
        employee_id: this.filters.currentEmployee,
        name: this.user_ids[this.filters.currentEmployee],
        segment_id: 1,
        phone: "",
        interlocutor: "Клиент",
        dayOfDelay: 0,
        date: moment().format("YYYY-MM-DD"),
      };

      let param_values = [];
      this.params.forEach((param, key) => {
        param_values.push({
          param_id: param.id,
          value: 0,
          record_id: 0,
        });
        obj["param" + key] = 0;
      });

      obj["param_values"] = param_values;
      obj["comments"] = "";
      obj["changed"] = true;
      obj["editable"] = true;
      this.records.data.unshift(obj);
    },

    saveRecord(record) {
      let loader = this.$loading.show();

      if (record.phone.length == 0) {
        this.$message.error("Укажите телефон!!!");
        loader.hide();
        return;
      }

      let obj = {
        id: record.id,
        employee_id: record.employee_id,
        segment_id: record.segment_id,
        phone: record.phone,
        interlocutor: record.interlocutor,
        dayOfDelay: record.dayOfDelay,
        date: record.date,
        param_values: record.param_values,
      };

      // this.params.forEach((param, key) => {
      //     obj['param' + key] = 0;
      // });

      obj["comments"] = record.comments;
      obj["group_id"] = this.currentGroup;

      axios
        .post("/timetracking/quality-control/save", obj)
        .then((response) => {
          console.log(response);
          if (response.data.method == "save") {
            record.id = response.data.id;
            record.total = response.data.total;
            record.segment = this.segment[record.segment_id];
            record.name = this.user_ids[record.employee_id];
            // this.records.data.shift()
            // this.records.data.unshift(record)
            this.$message.success("Сохранено");
          }
          if (response.data.method == "update") {
            this.$message.success("Изменено");
          }
          record.changed = false;
          this.$bvModal.hide("bv-modal");
          loader.hide();
        })
        .catch(function (e) {
          loader.hide();
          alert(e);
        });
    },

    deleteRecordModal(record, index) {
      this.deletingElementIndex = index;
      this.newRecord.id = record.id;
      this.newRecord.name = record.name;
      this.newRecord.interlocutor = record.interlocutor;
      this.newRecord.employee_id = record.employee_id;
      this.newRecord.phone = record.phone;
      this.newRecord.dayOfDelay = record.dayOfDelay;
      this.newRecord.date = record.date;
      this.newRecord.param1 = record.param1;
      this.newRecord.param2 = record.param2;
      this.newRecord.param3 = record.param3;
      this.newRecord.param4 = record.param4;
      this.newRecord.param5 = record.param5;
      this.newRecord.total = record.total;
      this.newRecord.comments = record.comments;
      this.$bvModal.show("delete-modal");
    },

    deleteRecord() {
      let loader = this.$loading.show();

      axios
        .post("/timetracking/quality-control/delete", {
          id: this.newRecord.id,
        })
        .then((response) => {
          this.$message.info("Запись #" + this.newRecord.id + " удалена");
          this.$bvModal.hide("delete-modal");

          // ES6 Func
          let index = this.records.data.findIndex(
            (x) => x.id === this.newRecord.id
          );
          this.records.data.splice(index, 1);

          this.newRecord.id = 0;
          loader.hide();
        });
    },

    setRecordsTable() {
      this.setRecordsTableFields();
      if (this.records.data.length > 0) this.records.data[0].editable = true;
    },

    editMode(item) {
      this.records.data.forEach((record, index) => {
        record.editable = false;
      });
      item.editable = true;
    },

    setRecordsTableFields() {
      let fieldsArray = [];
      let order = 1;

      if (this.currentGroup == 42) {
        fieldsArray.push({
          key: "segment",
          name: "Сегмент",
          type: "select",
          order: order++,
          klass: " text-center px-1 segment-width",
        });
      }

      fieldsArray.push({
        key: "phone",
        name: "Номер",
        typ: "text",
        order: order++,
        klass: " text-center px-1 phoner",
      });

      if (this.currentGroup == 42) {
        fieldsArray.push({
          key: "dayOfDelay",
          name: "День просрочки",
          type: "date",
          order: order++,
          klass: " text-center px-1 ",
        });
      }

      fieldsArray.push({
        key: "interlocutor",
        name: "Собеседник",
        type: "text",
        order: order++,
        klass: " text-center px-1 ",
      });

      fieldsArray.push({
        key: "date",
        name: "Дата прослушки",
        type: "date",
        order: order++,
        klass: " text-center px-1 ",
      });

      this.params.forEach((param, k) => {
        fieldsArray.push({
          key: "param" + k,
          name: param.name,
          type: "number",
          order: order++,
          klass: "text-center px-1 arg number",
        });
      });

      fieldsArray.push({
        key: "total",
        name: "Сумма оценки",
        type: "auto",
        order: order++,
        klass: " text-center px-1 number",
      });

      fieldsArray.push({
        key: "comments",
        name: "Комментарии",
        type: "text",
        order: order++,
        klass: " text-center px-1 comments",
      });

      this.recordFields = fieldsArray;
    },

    setMonthsTableFields() {
      let fieldsArray = [];
      let order = 1;

      fieldsArray.push({
        key: "total",
        name: "Итог",
        order: order++,
        klass: " text-center px-1 t-total",
      });

      fieldsArray.push({
        key: "quantity",
        name: "N",
        order: order++,
        klass: " text-center px-1 t-quantity",
      });

      for (let i = 1; i <= 12; i++) {
        if (i.length == 1) i = "0" + i;

        fieldsArray.push({
          key: i,
          name: moment(this.currentYear + "-" + i + "-01").format("MMMM"),
          order: order++,
          klass: "text-center px-1 month",
        });
      }

      this.monthFields = fieldsArray;
    },

    calcTotalWeekField() {
      let weekly_totals = [];

      this.fields.forEach((field) => {
        let total = 0;
        let count = 0;
        let key = field.key;
        this.items.forEach((item, index) => {
          if (item.weeks[key] !== undefined && Number(item.weeks[key]) > 0) {
            total += Number(item.weeks[key]);
            count++;
          }
        });

        weekly_totals[key] = count > 0 ? Number(total / count).toFixed(0) : 0;
      });

      this.items.unshift({
        id: 0,
        name: "",
        months: {},
        weeks: weekly_totals,
      });
    },

    setWeeksTableFields() {
      let fieldsArray = [];
      let weekNumber = 1;
      let order = 1;

      fieldsArray.push({
        key: "total",
        name: "Итог",
        order: order++,
        klass: " text-center px-1 t-total",
      });

      for (let i = 1; i <= this.monthInfo.daysInMonth; i++) {
        let m = this.monthInfo.month.toString();
        let d = i;
        if (d.toString().length == 1) d = "0" + d;
        if (m.length == 1) m = "0" + m;
        //console.log(this.currentYear + '-' + m + '-' + d)

        let date = moment(this.currentYear + "-" + m + "-" + d);
        let dow = date.day();

        fieldsArray.push({
          key: i,
          name: i,
          order: order++,
          klass: "text-center px-1",
          type: "day",
        });

        if (dow == 0) {
          fieldsArray.push({
            key: "avg" + weekNumber,
            name: "Ср. " + weekNumber,
            order: order++,
            klass: "text-center px-1 averages",
            type: "avg",
          });
          weekNumber++;
        }

        if (dow != 0 && i == this.monthInfo.daysInMonth) {
          fieldsArray.push({
            key: "avg" + weekNumber,
            name: "Ср. " + weekNumber,
            order: order++,
            klass: "text-center px-1 averages",
            type: "avg",
          });
        }
      }

      this.fields = fieldsArray;
    },

    updateWeekValue(item, key) {
      console.log(key);
      console.log(item);

      let loader = this.$loading.show();

      axios
        .post("/timetracking/quality-control/saveweekly", {
          day: key,
          month: this.monthInfo.month,
          year: this.currentYear,
          total: item.weeks[key],
          user_id: item.id,
          group_id: this.currentGroup,
        })
        .then((response) => {
          console.log(response);
          this.$message.success("Сохранено");
          loader.hide();
        })
        .catch(function (e) {
          loader.hide();
          alert(e);
        });
    },

    changeStat(record) {
      this.params.forEach((param, k) => {
        if (record["param" + k] < 0) record["param" + k] = 0;
        if (record["param" + k] > 100) record["param" + k] = 100;

        if (record.param_values[k] !== undefined) {
          record.param_values[k].value = Number(record["param" + k]);
        } else {
          record.param_values[k] = {
            id: 0,
            param_id: param.id,
            record_id: record.id,
            value: Number(record["param" + k]),
          };
        }

        // r//ecord['param' + k] = Number(record.param_values[k].value);
        total += Number(record["param" + k]);
      });

      record.changed = true;

      let total = 0;

      this.params.forEach((param, k) => {
        record.param_values[k].value = Number(record["param" + k]);
        total += Number(record["param" + k]);
      });

      if (Number(total) > 100) total = 100;
      record.total = Number(total);
      //if(this.params.length > 0) record.total = Number(Number(total / this.params.length).toFixed(0));
      //record.total = Number(record.param1) + Number(record.param2) + Number(record.param3) + Number(record.param4) + Number(record.param5)
    },

    setYear() {
      this.currentYear = this.currentYear
        ? this.currentYear
        : this.$moment().format("YYYY");
    },

    setMonth() {
      this.monthInfo.currentMonth = this.monthInfo.currentMonth
        ? this.monthInfo.currentMonth
        : this.$moment().format("MMMM");
      this.monthInfo.month = this.$moment(
        this.monthInfo.currentMonth,
        "MMMM"
      ).format("M");

      let currentMonth = this.$moment(this.monthInfo.currentMonth, "MMMM");
      //Расчет выходных дней
      this.monthInfo.monthEnd = currentMonth.endOf("month"); //Конец месяца
      this.monthInfo.weekDays = currentMonth.weekdayCalc(
        currentMonth.startOf("month").toString(),
        currentMonth.endOf("month").toString(),
        [6]
      ); //Колличество выходных
      this.monthInfo.daysInMonth = new Date(
        this.currentYear,
        this.$moment(this.monthInfo.currentMonth, "MMMM").format("M"),
        0
      ).getDate(); //Колличество дней в месяце
      this.monthInfo.workDays =
        this.monthInfo.daysInMonth - this.monthInfo.weekDays; //Колличество рабочих дней
    },

    toFloat(number) {
      return Number(number).toFixed(0);
    },

    // ucalls or local grades
    change_type() {
      let e = null;

      if (this.can_add_records) {
        e = confirm("Перевести в автоматическую оценку с U-calls?");
      } else {
        e = confirm("Перевести в ручную оценку?");
      }

      if (e) {
        let loader = this.$loading.show();
        axios
          .post("/timetracking/quality-control/change-type", {
            type: this.can_add_records ? "ucalls" : "local",
            group_id: this.currentGroup,
          })
          .then((response) => {
            this.$message.success("Сохранено!");
            this.fetchData();
            loader.hide();
          })
          .catch(function (e) {
            loader.hide();
            alert(e);
          });
      }
    },

    exportData() {
      var link = "/timetracking/quality-control/export";
      link += "?group_id=" + this.currentGroup;
      link += "&day=" + this.currentDay;
      link += "&month=" + this.monthInfo.month;
      link += "&year=" + this.currentYear;
      window.location.href = link;
    },

    exportAll() {
      var link = "/timetracking/quality-control/exportall";
      link += "?month=" + this.monthInfo.month;
      link += "&group_id=" + this.currentGroup;
      link += "&year=" + this.currentYear;
      window.location.href = link;
    },

    changeTab() {
      console.log("tab changed");
    },
  },
};
</script>