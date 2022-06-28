<template>
<div class="mt-2 px-3 analytics-page">
    <div class="row mb-3 ">
        <div class="col-3">
            <select class="form-control" v-model="currentGroup" @change="fetchData">
                <option v-for="group in groups" :value="group.id" :key="group.id">{{group.name}}</option>
            </select>
        </div>
        <div class="col-3">
            <select class="form-control" v-model="monthInfo.currentMonth" @change="fetchData">
                <option v-for="month in $moment.months()" :value="month" :key="month">{{month}}</option>
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
        <div class="col-3">
           
        </div>
    </div>
    <div> 
        
        <div v-if="!firstEnter">
            <div v-if="this.hasPremission">
                
                <a-tabs type="card" v-if="dataLoaded" :defaultActiveKey='active'>


                    <template v-if="currentGroup == 48">
                        <a-tab-pane tab="Сводная" key="1">
                            <t-recruiter-stats :data="recruiting.recruiterStats" 
                                            :daysInMonth="new Date().getDate()"
                                            :rates="recruiting.recruiterStatsRates"
                                            :year="currentYear"
                                            :month="monthInfo.month"
                                            :leads_data="recruiting.recruiter_stats_leads"
                                            :editable="true"></t-recruiter-stats> 
                            <div class="mb-5"></div>
                            <g-recruting :records="recruiting.indicators" v-if="recruiting.indicators" :isAnalyticsPage="true"></g-recruting>
                            <div class="mb-5"></div>
                            <t-summary-recruting :records="recruiting.recrutingTotals" :month="monthInfo"></t-summary-recruting> 
                            <div class="mt-5"></div>

                            <t-decomposition 
                                :month="monthInfo"
                                :data="decomposition"
                            ></t-decomposition>
                        </a-tab-pane>
                        <a-tab-pane tab="Подробная" key="2">
                            <t-recruting-user v-for="hr in recruiting.hrs" :key="hr.id" 
                                :records="hr.records"
                                :name="hr.name"
                                :id="hr.id"
                                :editable="true"
                                :deleted="hr.deleted"
                                :hr="hr"
                                :month="monthInfo"
                                :workdays="hr.workdays"></t-recruting-user>

                            <div class="mb-2"><b>Архивированы</b></div>
                            <div v-for="hr in recruiting.hrs">
                                <div v-if="hr.deleted == true">{{ hr.name }}</div>
                            </div>
                        </a-tab-pane>
                        <a-tab-pane tab="Стажеры" key="3">
                            <t-skypes :month="monthInfo" 
                                :skypes="recruiting.skypes"
                                :groups="recruiting.sgroups"
                                :invite_groups="recruiting.invite_groups"
                                :segments="recruiting.segments"></t-skypes>
                        </a-tab-pane>
                        <a-tab-pane  key="4">
                            <span slot="tab">
                                <b class="roman">II</b> Этап стажировки
                            </span>

                            <a-tabs type="card">

                                <a-tab-pane tab="Сводная" key="1">
                                    <table class="table b-table table-striped table-bordered table-sm"  style="width:900px">
                                        <thead>
                                            <th class="text-left t-name table-title" style="background:#90d3ff;width:250px;">Группа</th>
                                            <th class="text-center t-name table-title">Требуется нанять</th>
                                            <th class="text-center t-name table-title">Кол-во <br>переданных <br> стажеров</th>
                                            <th class="text-center t-name table-title">Кол-во <br>приступивших <br>к работе</th>
                                            <th class="text-center t-name table-title">Процент <br>прохождения<br> стажировки</th>
                                            <th class="text-center t-name table-title">Кол-во<br> стажирующихся активных<br> <date-picker value="test"  :placeholder="trainee_date" v-model="trainee_date" @change="getTraineesByDate()"></date-picker> </th>
                                        </thead>
                                        <tbody v-for="ocenka in recruiting.ocenka_svod">
                                            <tr>
                                                <td class="text-left t-name table-title align-middle" style="background:#90d3ff">{{ ocenka.name }}</td>
                                                <td class="text-center t-name table-title align-middle">{{ ocenka.required }}</td>
                                                <td class="text-center t-name table-title align-middle">{{ ocenka.sent }}</td>
                                                <td class="text-center t-name table-title align-middle">{{ ocenka.working }}</td>
                                                <td class="text-center t-name table-title align-middle">{{ ocenka.percent }}</td>
                                                <td class="text-center t-name table-title align-middle">{{ ocenka.active }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </a-tab-pane>

                                <a-tab-pane tab="Оценка тренера" key="2">
                                    

                                    <trainee-report :trainee_report="recruiting.trainee_report" :groups="groups"></trainee-report>
                                  
                                    
                                </a-tab-pane>
                                <a-tab-pane tab="Оценка тренера за месяц" key="3">
                                    
                                    <svod-table :trainee_report="recruiting.trainee_report" :groups="groups" ></svod-table>
                                  
                                    
                                </a-tab-pane>
                                <a-tab-pane tab="Отсутствие стажеров" key="4">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <table class="table b-table table-striped table-bordered table-sm">
                                                <thead>
                                                    <th class="text-left t-name table-title" colspan="2">Первый день</th>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="absent in recruiting.absents_first" :key="absent.id">
                                                        <td class="text-left t-name table-title">{{ absent.cause }}</td>
                                                        <td class="text-center t-name table-title mw30">{{ absent.count }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-md-4">
                                            <table class="table b-table table-striped table-bordered table-sm">
                                                <thead>
                                                    <th class="text-left t-name table-title" colspan="2">Второй день</th>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="absent in recruiting.absents_second" :key="absent.id">
                                                        <td class="text-left t-name table-title">{{ absent.cause }}</td>
                                                        <td class="text-center t-name table-title">{{ absent.count }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-md-4">
                                            <table class="table b-table table-striped table-bordered table-sm">
                                                <thead>
                                                    <th class="text-left t-name table-title" colspan="2">После третьего дня</th>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="absent in recruiting.absents_third" :key="absent.id">
                                                        <td class="text-left t-name table-title">{{ absent.cause }}</td>
                                                        <td class="text-center t-name table-title">{{ absent.count }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </a-tab-pane>
                            </a-tabs>
                            

                            

                            
                        </a-tab-pane>
                        

                        

                        <a-tab-pane tab="Воронка" key="7">
                            <a-tabs type="card" v-if="dataLoaded" defaultActiveKey="0">
                                <a-tab-pane tab="Сводная" key="0">
                                    <t-funnel class="mb-5" :id="0" :table="recruiting.funnels['all']['all']" title="Сводная таблица" segment="segments" type="month" :date="date" />
                                    <t-funnel class="mb-5" :id="1" :table="recruiting.funnels['all']['hh']" title="hh" segment="hh" type="month" :date="date" />
                                    <t-funnel class="mb-5" :id="2" :table="recruiting.funnels['all']['insta']" title="Инста" segment="insta" type="month" :date="date" />
                                </a-tab-pane>
                                <template v-for="(month, i) in months">
                                    <a-tab-pane :tab="month.month" :key="i">
                                        <t-funnel class="mb-5" :table="recruiting.funnels['month'][i]['hh']" title="hh" segment="hh" type="week" :date="month.date" :key="5 * 1000 * (Number(i) +  10 * Number(i))"/>
                                        <t-funnel class="mb-5" :table="recruiting.funnels['month'][i]['insta']" title="Инста" segment="insta" type="week" :date="month.date" :key="6 * 1000 * (Number(i) +  10 * Number(i))"/>
                                    </a-tab-pane>
                                </template>
                                
                            </a-tabs>
                        </a-tab-pane>

                        <a-tab-pane  key="8">

                            <span slot="tab">
                                <b class="roman">IV</b> Увольнение
                            </span>

                            <a-tabs>
                                <a-tab-pane tab="Причины и процент текучки" key="1">
                                    <table-staff-turnover
                                        :staff="recruiting.staff"
                                        :causes="recruiting.causes"
                                        :staff_longevity="recruiting.staff_longevity"
                                        :staff_by_group="recruiting.staff_by_group"
                                        />
                                </a-tab-pane>
                            

                                <a-tab-pane tab="Причины: Бот" key="2">
                                    
                                    <div class="d-flex flex-wrap">

                                
                                    <template v-for="(quizz, key) in recruiting.quiz">
                                        <div class="question-wrap">
                                            <p> {{ quizz['q']}}</p>
                                            <div v-if="quizz['type'] == 'answer'">
                                                <div v-for="answer in quizz['answers']" :key="answer.id" class="d-flex">
                                                    <p class="fz12">{{ answer.text }}</p>
                                                </div>
                                            </div>
                                            <div v-if="quizz['type'] == 'variant'">
                                                <div v-for="answer in quizz['answers']" :key="answer.id" class="d-flex">
                                                    <progress-bar
                                                        :percentage="Number(answer.percent)"
                                                        :label="answer.text + ' (' + answer.count + ')'"
                                                        :class="'active'"
                                                        ></progress-bar>
                                                </div>
                                            </div>

                                            <div v-if="quizz['type'] == 'star'">
                                                <div v-for="answer in quizz['answers']" :key="answer.id" class="d-flex">
                                                    <Rating :grade="Number(answer.text).toFixed(0)" :maxStars="10" :hasCounter="false" />
                                                    <p class="mb-0">{{ answer.text + ' (' + answer.count + ')' }}</p>
                                                </div>
                                            </div>
                                            
                                        </div>
                                        
                                    </template>
                                    </div>
                                </a-tab-pane>
                                <a-tab-pane tab="Причины увольнения" key="3">
                                    <div class="col-md-12 col-lg-6 d-flex align-items-center">
                                        <table class="table b-table table-striped table-bordered table-sm">
                                            <thead>
                                                <th class="text-left t-name table-title" colspan="2">Причины увольнения</th>
                                            </thead>
                                            <tbody>
                                                <tr v-for="cause in recruiting.causes" :key="cause.id">
                                                    <td class="text-left t-name table-title">{{ cause.cause }}</td>
                                                    <td class="text-center t-name table-title">{{ cause.count }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </a-tab-pane>
                            </a-tabs>
                        </a-tab-pane>
                    </template>
                    
                </a-tabs>
            </div>


            <div v-else>
                <p>У вас нет доступа к этой группе</p>
            </div>
            
            <div class="empty-space"></div>
        </div>
    </div>

</div>
</template>

<script>
import TableStaffTurnover from '../components/tables/TableStaffTurnover.vue';
import Rating from '../components/ui/Rating.vue';
export default {
  components: { TableStaffTurnover, Rating },
    name: "Analytics", 
    props: ['groups', 'activeuserid'],
    data() {
        return {
            trainee_date: new Date(Date.now()).toISOString().substring(0, 10),
            totals: [],
            fn: '',
            data: [],
            active: '1',
            activities: [],
            report_group_id: 42,
            call_bases: [],
            call_bases_key: 1,
            archived_recruiters: [],
            utility: [], // gauges
            recruiting: {
                recrutingTotals: [],
                recruiterStats: [],
                recruiterStatsRates: [],
                recruiter_stats_leads: [],
                funnels: [],
                quiz: [], // 48 uvolennye
                trainee_report: [], // 
                causes: [],
                staff: [],
                staff_by_group: [],
                staff_longevity: [],
                ocenka_svod: [],
                //ratings: [],
                //ratings_dates: [],
                //ratings_heads: [],
                absents_first: [],
                absents_second: [],
                indicators: null,
                invite_groups: [], // 48
                sgroups: [], // 48
                skypes: [], // 48
                hrs: [],
                segments: [],
            },
            quality: [],
            records: [],
            hasPremission: false,
            firstEnter: true,
            years: [2020, 2021, 2022],
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
            dataLoaded: false,
            currentGroup: null,
            minutes: {},
            loader: null,
            date: null,
            coef: 0,
            months: {
                1: {month:'Январь', date: null},
                2: {month:'Февраль', date: null},
                3: {month:'Март', date: null},
                4: {month:'Апрель', date: null},
                5: {month:'Май', date: null},
                6: {month:'Июнь', date: null},
                7: {month:'Июль', date: null},
                8: {month:'Август', date: null},
                9: {month:'Сентябрь', date: null},
                10: {month:'Октябрь', date: null},
                11: {month:'Ноябрь', date: null},
                12: {month:'Декабрь', date: null},
            },
            componentKeys: {
                5: 0,
                6: 100000,
                7: 200000,
                8: 300000,
                9: 400000,
            },
        }
    },
    created() {

        // бывор группы
        const urlParams = new URLSearchParams(window.location.search);
        let group = urlParams.get('group');
        let active = urlParams.get('active');
        let load = urlParams.get('load');
        this.currentGroup = (group == null) ? this.groups[0].id : parseFloat(group)
        this.active = (active == null) ? '1' : active

        this.setMonth()
        this.setYear()
        this.setMonthsObject()

        if(load != null) {
            this.fetchData()
        }

    },
    methods: {

        setMonthsObject() {

            for(let i = 1; i<=12;i++) {
                let month = i;
                if(i < 10) {month = '0' + i;}

                this.months[i].date = this.currentYear + '-' + month + '-' + '01';
            }
        },
        setMonth() {
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
            
        },
        //Установка выбранного года 
        setYear() {
            this.currentYear = this.currentYear ? this.currentYear : this.$moment().format('YYYY')
            this.monthInfo.currentYear = this.currentYear;
        },

        getTotals(data) { 
            axios.post('/timetracking/get-totals-of-reports', {
                    month: this.$moment(this.monthInfo.currentMonth, 'MMMM').format('M'),
                    year: this.currentYear,
                    group_id: this.currentGroup
                })
                .then(response => {
                    this.totals = response.data.sum
                    this.data = data
                })
                .catch(error => console.log('Error GetTotals'))
        },

        fetchData() {
            let loader = this.$loading.show();


            axios.post('/timetracking/getanalytics', {
                month: this.$moment(this.monthInfo.currentMonth, 'MMMM').format('M'),
                year: this.currentYear,
                group_id: this.currentGroup
            }).then(response => {
                if (response.data.error && response.data.error == 'access') {
                    this.hasPremission = false
                    loader.hide();
                    return;
                }
                this.hasPremission = true
                
                this.setMonth()
                this.setYear()
                
                this.dataLoaded = true
                this.firstEnter = false
               
                this.componentKeys[5]++

                if(this.currentGroup == 48) { // recruiting
                    this.recruiting.hrs = response.data.hrs
                    this.recruiting.skypes = response.data.skypes
                    this.recruiting.sgroups = response.data.sgroups
                    this.recruiting.invite_groups = response.data.invite_groups
                    this.recruiting.indicators = response.data.indicators
                    this.recruiting.absents_first = response.data.absents_first
                    this.recruiting.absents_second = response.data.absents_second
                    this.recruiting.absents_third = response.data.absents_third
                   
                    this.recruiting.staff = response.data.staff
                    this.recruiting.staff_by_group = response.data.staff_by_group
                    this.recruiting.staff_longevity = response.data.staff_longevity
                    this.recruiting.causes = response.data.causes
                    this.recruiting.quiz = response.data.quiz
                    this.recruiting.recrutingTotals = response.data.records
                    this.recruiting.recruiterStats = response.data.recruiter_stats
                    this.recruiting.recruiterStatsRates = response.data.recruiter_stats_rates
                    this.recruiting.ocenka_svod = response.data.ocenka_svod
                    
                    this.recruiting.trainee_report = response.data.trainee_report
                    this.recruiting.recruiter_stats_leads = response.data.recruiter_stats_leads
                    this.recruiting.funnels = response.data.funnels
                    this.recruiting.segments = response.data.segments
                    this.decomposition = response.data.decomposition

                    this.archived_recruiters = localStorage;
                    
                    this.date = response.data.date
                    this.setMonthsObject();
                    this.componentKeys[6]++
                    this.componentKeys[7]++
                    this.componentKeys[8]++
                    this.componentKeys[9]++
                }
                window.history.replaceState({ id: "100" }, "Аналитика групп", "/timetracking/analytics?group=" + this.currentGroup + "&active=" + this.active);
                this.monthInfo.workDays = this.work_days = this.getBusinessDateCount(this.monthInfo.month,this.monthInfo.currentYear, response.data.workdays)
                loader.hide()
            }).catch(error => {
                loader.hide()
                alert(error)
            });
        },
        getTraineesByDate(){
            axios.post('/timetracking/getactivetrainees',{date: this.trainee_date}).then(response => {
                console.log(response.data.ocenka_svod);
                this.recruiting.ocenka_svod = response.data.ocenka_svod;
            });
        },
         getBusinessDateCount(month, year, workdays) {
  
            month = month - 1; 
            let next_month = (month + 1) == 12 ? 0 : month + 1; 
            let next_year = (month + 1) == 12 ? year + 1 : year; 

            var start = new Date(year, month, 1);
            var end = new Date(next_year, next_month, 1);

            let days = (end - start) / 86400000;

            let business_days = 0,
                weekends = workdays == 5 ? [0,6] : [0];

            for(let i = 1; i <= days; i++) {
                let d = new Date(year, month, i).getDay(); 
                if(!weekends.includes(d)) business_days++;
            }
            
            return business_days;
        },

    } 
}
</script>

<style>
.mw30 {
    min-width: 30px;
}
.rating {
  display: inline-block;
  unicode-bidi: bidi-override;
  color: #888888;
  font-size: 25px;
  height: 25px;
  width: auto;
  margin: 0;
  position: relative;
  padding: 0;
}

.rating-upper {
  color: #c52b2f;
  padding: 0;
  position: absolute;
  z-index: 1;
  display: flex;
  top: 0;
  left: 0;
  overflow: hidden;
}

.rating-lower {
  padding: 0;
  display: flex;
  z-index: 0;
}

.analytics-page .btn {
    padding: .375rem .75rem;
}
.analytics-page .btn.btn-sm {
    padding: 0.15rem 0.5rem;
}
.fz12 {
    font-size: 12px;
    margin-bottom: 0;
    line-height: 20px;
    color: #000 !important;
}
.wrap {
    background: aliceblue;
    margin-bottom: 10px;
    padding-top: 15px;
}
.ramka {
    border: 1px solid #dee2e6;
    box-shadow: 0 8px 10px 10px #f7f7f7;
    padding: 15px;
}
.date-select {
    width: 250px;  
}
</style> 
