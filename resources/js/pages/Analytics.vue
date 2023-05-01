<template>
	<div
		v-if="groups"
		class="analytics-page"
	>
		<!-- header -->
		<div class="row mb-4">
			<div class="col-3">
				<select
					class="form-control"
					v-model="currentMonth"
				>
					<option
						v-for="(month, index) in $moment.months()"
						:value="index"
						:key="month"
					>
						{{ month }}
					</option>
				</select>
			</div>
			<div class="col-2">
				<select
					class="form-control"
					v-model="currentYear"
				>
					<option
						v-for="year in years"
						:value="year"
						:key="year"
					>
						{{ year }}
					</option>
				</select>
			</div>
			<div class="col-1">
				<div
					class="btn btn-primary rounded"
					@click="onChangeTab(activeTab)"
				>
					<i class="fa fa-redo-alt" />
				</div>
			</div>
			<div class="col-3" />
		</div>

		<!-- tabs -->
		<div>
			<div v-if="isReady">
				<div v-if="hasPremission">
					<b-tabs
						v-model="activeTab"
						type="card"
						:default-active-key="active"
					>
						<template v-if="currentGroup == 48">
							<b-tab
								title="Сводная"
								key="1"
								card
							>
								<TableRecruiterStats
									:data="recruiterStats"
									:days-in-month="new Date().getDate()"
									:rates="recruiterStatsRates"
									:year="currentYear"
									:month="currentMonth + 1"
									:leads_data="recruiterStatsLeads"
									:editable="true"
									@changeDay="setDay"
								/>
								<div class="mb-5" />
								<Recruting
									v-if="indicators"
									:is-analytics-page="true"
									:records="indicators"
								/>
								<div class="mb-5" />
							</b-tab>
							<b-tab
								title="Стажеры"
								key="3"
								card
							>
								<TableSkype
									v-if="skypes.data"
									:month="monthInfo"
									:skypes="skypes.data"
									:groups="sgroups"
									:invite_groups="inviteGroups"
									:segments="segments"
								/>
							</b-tab>
							<b-tab
								key="4"
								card
							>
								<template #title>
									<b-spinner
										type="grow"
										small
									/> <b class="roman">II</b> Этап стажировки
								</template>


								<b-tabs type="card">
									<b-tab
										title="Сводная"
										key="1"
										card
									>
										<table
											class="table b-table table-striped table-bordered table-sm"
											style="width:900px"
										>
											<thead>
												<th
													class="text-left t-name table-title"
													style="background:#90d3ff;width:250px;"
												>
													Отдел
												</th>
												<th class="text-center t-name table-title">
													Требуется нанять
												</th>
												<th class="text-center t-name table-title">
													Кол-во <br>переданных <br> стажеров
												</th>
												<th class="text-center t-name table-title">
													Кол-во <br>приступивших <br>к работе
												</th>
												<th class="text-center t-name table-title">
													Процент <br>прохождения<br> стажировки
												</th>
												<th class="text-center t-name table-title">
													Кол-во<br> стажирующихся активных
													<i
														class="fa fa-info-circle"
														v-b-popover.hover.right.html="'Стажеры, которые присутстовали на сегодня. В табели у них есть оранжевая отметка.'"
														title="Активные стажеры"
													/>
												</th>
											</thead>
											<tbody
												v-for="(ocenka, index) in ocenkaSvod"
												:key="index"
											>
												<tr>
													<td
														class="text-left t-name table-title align-middle"
														style="background:#90d3ff"
													>
														{{ ocenka.name }}
													</td>
													<td class="text-center t-name table-title align-middle">
														{{ ocenka.required }}
													</td>
													<td class="text-center t-name table-title align-middle">
														{{ ocenka.sent }}
													</td>
													<td class="text-center t-name table-title align-middle">
														{{ ocenka.working }}
													</td>
													<td class="text-center t-name table-title align-middle">
														{{ ocenka.percent }}
													</td>
													<td class="text-center t-name table-title align-middle">
														{{ ocenka.active }}
													</td>
												</tr>
											</tbody>
										</table>
									</b-tab>

									<!--<b-tab title="Оценка тренера" key="2">


                                    <trainee-report :trainee_report="recruiting.trainee_report" :groups="groups"></trainee-report>


                                </b-tab>-->
									<b-tab
										title="Оценка тренера"
										key="2"
										card
									>
										<SvodTable
											:trainee_report="traineeReport"
											:groups="groups"
										/>
									</b-tab>
									<b-tab
										title="Отсутствие стажеров"
										key="4"
										card
									>
										<div class="row">
											<div class="col-md-4">
												<table class="table b-table table-striped table-bordered table-sm">
													<thead>
														<th
															class="text-left t-name table-title"
															colspan="2"
														>
															Первый день
														</th>
													</thead>
													<tbody>
														<tr
															v-for="absent in absentsFirst"
															:key="absent.id"
														>
															<td class="text-left t-name table-title">
																{{ absent.cause }}
															</td>
															<td class="text-center t-name table-title mw30">
																{{ absent.count }}
															</td>
														</tr>
													</tbody>
												</table>
											</div>
											<div class="col-md-4">
												<table class="table b-table table-striped table-bordered table-sm">
													<thead>
														<th
															class="text-left t-name table-title"
															colspan="2"
														>
															Второй день
														</th>
													</thead>
													<tbody>
														<tr
															v-for="absent in absentsSecond"
															:key="absent.id"
														>
															<td class="text-left t-name table-title">
																{{ absent.cause }}
															</td>
															<td class="text-center t-name table-title">
																{{ absent.count }}
															</td>
														</tr>
													</tbody>
												</table>
											</div>
											<div class="col-md-4">
												<table class="table b-table table-striped table-bordered table-sm">
													<thead>
														<th
															class="text-left t-name table-title"
															colspan="2"
														>
															После третьего дня
														</th>
													</thead>
													<tbody>
														<tr
															v-for="absent in absentsThird"
															:key="absent.id"
														>
															<td class="text-left t-name table-title">
																{{ absent.cause }}
															</td>
															<td class="text-center t-name table-title">
																{{ absent.count }}
															</td>
														</tr>
													</tbody>
												</table>
											</div>
										</div>
									</b-tab>
								</b-tabs>
							</b-tab>

							<b-tab
								title="Воронка"
								key="7"
								card
							>
								<b-tabs
									type="card"
									v-if="funnels.all"
									default-active-key="0"
								>
									<b-tab
										title="Сводная"
										key="0"
										card
									>
										<div class="row">
											<div class="col-8">
												<TableFunnel
													class="mb-5"
													:id="0"
													:table="funnels['all']['all']"
													title="Сводная таблица"
													segment="segments"
													type="month"
													:date="date"
												/>
												<TableFunnel
													class="mb-5"
													:id="1"
													:table="funnels['all']['hh']"
													title="hh.ru"
													segment="hh"
													type="month"
													:date="date"
												/>
												<TableFunnel
													class="mb-5"
													:id="2"
													:table="funnels['all']['insta']"
													title="Job.bpartners.kz"
													segment="insta"
													type="month"
													:date="date"
												/>
											</div>

											<!-- partner link creator -->
											<div class="col-4">
												<ref-linker />
											</div>
										</div>
									</b-tab>
									<b-tab
										v-for="(month, i) in months"
										:key="i"
										:title="month.month"
										card
									>
										<TableFunnel
											class="mb-5"
											:table="funnels['month'][i]['hh']"
											title="hh.ru"
											segment="hh"
											type="week"
											:date="month.date"
											:key="5 * 1000 * (Number(i) + 10 * Number(i))"
										/>
										<TableFunnel
											class="mb-5"
											:table="funnels['month'][i]['insta']"
											title="Job.bpartners.kz"
											segment="insta"
											type="week"
											:date="month.date"
											:key="6 * 1000 * (Number(i) + 10 * Number(i))"
										/>
									</b-tab>
								</b-tabs>
							</b-tab>

							<b-tab
								key="8"
								card
							>
								<template #title>
									<b-spinner
										type="grow"
										small
									/> <b class="roman">IV</b> Увольнение
								</template>


								<b-tabs>
									<b-tab
										title="Причины и процент текучки"
										key="1"
										card
									>
										<TableStaffTurnover
											:staff="staff"
											:causes="causes"
											:staff_longevity="staffLongevity"
											:staff_by_group="staffByGroup"
										/>
									</b-tab>


									<b-tab
										title="Причины: Бот"
										key="2"
										card
									>
										<div class="d-flex flex-wrap">
											<div
												v-for="(quizz, key) in quiz"
												:key="key"
												class="question-wrap"
											>
												<p> {{ quizz['q'] }}</p>
												<div v-if="quizz['type'] == 'answer'">
													<div
														v-for="answer in quizz['answers']"
														:key="answer.id"
														class="d-flex"
													>
														<p class="fz12">
															{{ answer.text }}
														</p>
													</div>
												</div>
												<div v-if="quizz['type'] == 'variant'">
													<div
														v-for="answer in quizz['answers']"
														:key="answer.id"
														class="d-flex"
													>
														<ProgressBar
															:percentage="Number(answer.percent)"
															:label="answer.text + ' (' + answer.count + ')'"
															:class="'active'"
														/>
													</div>
												</div>

												<div v-if="quizz['type'] == 'star'">
													<div
														v-for="answer in quizz['answers']"
														:key="answer.id"
														class="d-flex"
													>
														<Rating
															:grade="Number(answer.text).toFixed(0)"
															:max-stars="10"
															:has-counter="false"
														/>
														<p class="mb-0">
															{{ answer.text + ' (' + answer.count + ')' }}
														</p>
													</div>
												</div>
											</div>
										</div>
									</b-tab>
									<b-tab
										title="Причины увольнения"
										key="3"
										card
									>
										<div class="col-md-12 col-lg-6 d-flex align-items-center">
											<table class="table b-table table-striped table-bordered table-sm">
												<thead>
													<th
														class="text-left t-name table-title"
														colspan="2"
													>
														Причины увольнения
													</th>
												</thead>
												<tbody>
													<tr
														v-for="cause in causes"
														:key="cause.id"
													>
														<td class="text-left t-name table-title">
															{{ cause.cause }}
														</td>
														<td class="text-center t-name table-title">
															{{ cause.count }}
														</td>
													</tr>
												</tbody>
											</table>
										</div>
									</b-tab>
								</b-tabs>
							</b-tab>
						</template>
					</b-tabs>
				</div>


				<div v-else>
					<p>У вас нет доступа к этой группе</p>
				</div>

				<div class="empty-space" />
			</div>
		</div>
		<Loading
			:active="isLoading"
			:can-cancel="false"
			:is-full-page="true"
		/>
	</div>
</template>

<script>
import Loading from 'vue-loading-overlay'
import TableStaffTurnover from '@/components/tables/TableStaffTurnover.vue'
import Rating from '@/components/ui/Rating.vue'
import TableRecruiterStats from '@/components/analytics/TableRecruiterStats' // Почасовая таблица рекрутинга
import Recruting from '@/components/analytics/Recruting' // сводная информация рекрутинг
import TableSkype from '@/components/tables/TableSkype' // Стажеры
import SvodTable from '@/components/SvodTable' //сводная таблица для аналитики
import TableFunnel from '@/components/tables/TableFunnel' // Воронка
import ProgressBar from '@/components/ProgressBar' // в ответах quiz
import { useYearOptions } from '@/composables/yearOptions'
import { useHRStore } from '@/stores/ReportsHR.js'
import { mapActions, mapState } from 'pinia'

export default {
	name: 'PageAnalytics',
	components: {
		Loading,
		TableStaffTurnover,
		Rating,
		TableRecruiterStats,
		Recruting,
		TableSkype,
		SvodTable,
		TableFunnel,
		ProgressBar,
	},
	props: {
		groups: {
			type: Array,
			default: () => []
		},
		activeuserid: {
			type: Number,
			default: 0
		},
	},
	data() {
		const now = new Date(Date.now())
		return {
			activeTab: 0,
			// trainee_date: now.toISOString().substring(0, 10),
			// totals: [],
			data: [],
			active: '1',
			years: useYearOptions(),
			currentYear: now.getFullYear(),
			currentMonth: now.getMonth(),
			currentDay: now.getDate(),
			currentGroup: 48,
			loader: null,
		}
	},
	watch: {
		groups(){
			this.init()
		},
		activeTab(value){
			this.onChangeTab(value)
		},
		currentYear(){
			this.onChangeTab(this.activeTab)
		},
		currentMonth(){
			this.onChangeTab(this.activeTab)
		},
		currentDay(){
			this.onChangeTab(this.activeTab)
		},
	},
	computed: {
		...mapState(useHRStore, [
			'isLoading',
			'isReady',
			'error',
			// recruiter
			'recruiterStats',
			'recruiterStatsLeads',
			'recruiterStatsRates',
			// indicators
			'indicatorsDate',
			'indicators',
			'records',
			'hrs', // ????
			// Trainees
			'inviteGroups',
			'segments',
			'sgroups',
			'skypes',
			// Internship
			'absentsFirst',
			'absentsSecond',
			'absentsThird',
			'ocenkaSvod',
			'traineeReport',
			// funnels
			'funnels',
			// Dismiss
			'causes',
			'quiz',
			'staff',
			'staffByGroup',
			'staffLongevity',
		]),
		months(){
			const months = {
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
			}
			Object.keys(months).forEach(key => {
				months[key].date = `${this.currentYear}-${key > 9 ? key : '0' + key}-01`
			})
			return months
		},
		monthInfo(){
			const now = new Date()
			return {
				currentMonth: this.currentMonth || this.$moment(now).format('MMMM')
			}
		},
		hasPremission(){
			return !this.error
		},
		date(){
			return `${this.currentYear}-${(this.currentMonth > 8 ? '' : '0') + (this.currentMonth + 1)}-${this.currentDay > 9 ? this.currentDay : '0' + this.currentDay}`
		},
	},
	created() {
		if(this.groups){
			this.init()
		}
	},
	methods: {
		...mapActions(useHRStore, [
			'fetchRecruitment',
			'fetchIndicators',
			'fetchTrainees',
			'fetchInternship',
			'fetchFunnels',
			'fetchDismiss',
		]),
		init(){
			// бывор группы
			const urlParams = new URLSearchParams(window.location.search)
			const group = urlParams.get('group')
			const active = urlParams.get('active')
			if(group == null){
				this.currentGroup = this.groups && this.groups[0] ? this.groups[0].id : ''
			}
			else{
				this.currentGroup = parseFloat(group)
			}
			this.active = (active == null) ? '1' : active

			this.fetchRecruitment({
				day: this.currentDay,
				month: this.currentMonth + 1,
				year: this.currentYear,
			})
			this.fetchIndicators({
				month: this.currentMonth + 1,
				year: this.currentYear,
			})
		},
		onChangeTab(tab){
			switch(tab){
			case 0:
				this.fetchRecruitment({
					day: this.currentDay,
					month: this.currentMonth + 1,
					year: this.currentYear,
				})
				this.fetchIndicators({
					month: this.currentMonth + 1,
					year: this.currentYear,
				})
				break
			case 1:
				this.fetchTrainees({
					month: this.currentMonth + 1,
					year: this.currentYear,
					limit: 200, // временно т.к. переделывать пагинацию это отдельная история, нужно реализовать фильтры на беке
				})
				break
			case 2:
				this.fetchInternship({
					month: this.currentMonth + 1,
					year: this.currentYear,
				})
				break
			case 3:
				this.fetchFunnels({
					month: this.currentMonth + 1,
					year: this.currentYear,
				})
				break
			case 4:
				this.fetchDismiss({
					month: this.currentMonth + 1,
					year: this.currentYear,
				})
				break
			}
		},
		setDay(value){
			this.currentDay = value
		},
		// getTotals(data) {
		// 	this.axios.post('/timetracking/get-totals-of-reports', {
		// 		month: this.currentMonth + 1,
		// 		year: this.currentYear,
		// 		group_id: this.currentGroup
		// 	})
		// 		.then(response => {
		// 			this.totals = response.data.sum
		// 			this.data = data
		// 		})
		// 		.catch(() => console.log('Error GetTotals'))
		// },
		// getTraineesByDate(){
		// 	this.axios.post('/timetracking/getactivetrainees',{date: this.trainee_date}).then(response => {
		// 		console.log(response.data.ocenka_svod);
		// 		this.recruiting.ocenka_svod = response.data.ocenka_svod;
		// 	});
		// },

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
