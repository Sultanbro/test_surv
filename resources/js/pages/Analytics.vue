<template>
	<div
		v-if="groups"
		class="analytics-page"
	>
		<!-- header -->
		<div class="row my-4">
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
				<JobtronButton
					small
					@click="onChangeTab(activeTab)"
				>
					<i class="fa fa-redo-alt" />
				</JobtronButton>
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


								<div class="pt-4">
									<b-tabs type="card">
										<b-tab
											title="Сводная"
											key="1"
											card
										>
											<TableTraineeSage2
												:ocenka-svod="ocenkaSvod"
												class="pt-4"
											/>
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
												class="pt-5"
											/>
										</b-tab>
										<b-tab
											title="Отсутствие стажеров"
											key="4"
											card
										>
											<div class="row pt-4">
												<div class="col-md-4">
													<JobtronTable
														:fields="[{key: 'cause', label: 'Первый день', colspan: 2, thClass: 'text-left', tdClass: 'text-left'}, {key: 'count', hide: true, tdClass:'text-center'}]"
														:items="absentsFirst"
													/>
												</div>
												<div class="col-md-4">
													<JobtronTable
														:fields="[{key: 'cause', label: 'Второй день', colspan: 2, thClass: 'text-left', tdClass: 'text-left'}, {key: 'count', hide: true, tdClass:'text-center'}]"
														:items="absentsSecond"
													/>
												</div>
												<div class="col-md-4">
													<JobtronTable
														:fields="[{key: 'cause', label: 'После третьего дня', colspan: 2, thClass: 'text-left', tdClass: 'text-left'}, {key: 'count', hide: true, tdClass:'text-center'}]"
														:items="absentsSecond"
													/>
												</div>
											</div>
										</b-tab>
									</b-tabs>
								</div>
							</b-tab>

							<b-tab
								title="Воронка"
								key="7"
								card
							>
								<div class="pt-4">
									<b-tabs
										v-if="funnels.all"
										type="card"
										default-active-key="0"
									>
										<b-tab
											title="Сводная"
											key="0"
											card
										>
											<div class="row pt-4">
												<div class="col-8">
													<div class="PageAnalytics-funnels">
														<TableFunnel
															class="mb-4"
															:id="0"
															:table="funnels['all']['all']"
															title="Сводная таблица"
															segment="segments"
															type="month"
															:date="date"
														/>
														<TableFunnel
															class="mb-4"
															:id="1"
															:table="funnels['all']['hh']"
															title="hh.ru"
															segment="hh"
															type="month"
															:date="date"
														/>
														<TableFunnel
															class="mb-4"
															:id="2"
															:table="funnels['all']['insta']"
															title="Job.bpartners.kz"
															segment="insta"
															type="month"
															:date="date"
														/>
													</div>
												</div>
												<!-- partner link creator -->
												<div class="col-4">
													<RefLinker />
												</div>
											</div>
										</b-tab>
										<b-tab
											v-for="(month, i) in months"
											:key="i"
											:title="month.month"
											card
										>
											<div class="pt-4">
												<TableFunnel
													class="mb-4"
													:table="funnels['month'][i]['hh']"
													title="hh.ru"
													segment="hh"
													type="week"
													:date="month.date"
													:key="5 * 1000 * (Number(i) + 10 * Number(i))"
												/>
												<TableFunnel
													class="mb-4"
													:table="funnels['month'][i]['insta']"
													title="Job.bpartners.kz"
													segment="insta"
													type="week"
													:date="month.date"
													:key="6 * 1000 * (Number(i) + 10 * Number(i))"
												/>
											</div>
										</b-tab>
									</b-tabs>
								</div>
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


								<div class="pt-4">
									<b-tabs>
										<b-tab
											title="Причины и процент текучки"
											key="1"
											card
										>
											<div class="pt-4">
												<TableStaffTurnover
													:staff="staff"
													:causes="causes"
													:staff_longevity="staffLongevity"
													:staff_by_group="staffByGroup"
												/>
											</div>
										</b-tab>
										<b-tab
											title="Причины: Бот"
											key="2"
											card
										>
											<div class="d-flex flex-wrap pt-4">
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
															class="row"
														>
															<div class="col-6">
																{{ answer.text + ' (' + answer.count + ')' }}
															</div>
															<div class="col-6">
																<div class="PageAnalytics-progress">
																	<div class="PageAnalytics-progressPercent">
																		{{ Number(answer.percent) || 0 }}%
																	</div>
																	<ProgressBar :progress="(Number(answer.percent) || 0) + '%'" />
																</div>
															</div>
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
											<div class="row">
												<div class="col-md-12 col-lg-6 d-flex align-items-center pt-4">
													<JobtronTable
														:fields="[{key: 'cause', label: 'Причины увольнения', colspan: 2, thClass: 'text-left', tdClass: 'text-left'}, {key: 'count', hide: true, tdClass:'text-center'}]"
														:items="absentsFirst"
													/>
												</div>
											</div>
										</b-tab>
									</b-tabs>
								</div>
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
import TableTraineeSage2 from '@/components/tables/TableTraineeSage2' // Стажеры
import SvodTable from '@/components/SvodTable' //сводная таблица для аналитики
import TableFunnel from '@/components/tables/TableFunnel' // Воронка
import ProgressBar from '@ui/ProgressBar'
import { useYearOptions } from '@/composables/yearOptions'
import { useHRStore } from '@/stores/ReportsHR.js'
import { mapActions, mapState } from 'pinia'
import JobtronTable from '@ui/Table'
import JobtronButton from '@ui/Button'
import { usePortalStore } from '@/stores/Portal'
import RefLinker from '@/components/RefLinker' // рефералки

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
		TableTraineeSage2,
		JobtronTable,
		JobtronButton,
		RefLinker,
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
		...mapState(usePortalStore, ['portal']),
		years(){
			if(!this.portal.created_at) return [new Date().getFullYear()]
			return useYearOptions(new Date(this.portal.created_at).getFullYear())
		},
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

<style lang="scss">
.PageAnalytics{
	&-progress{
		display: flex;
		align-items: center;
		justify-content: flex-start;
		gap: 10px;
		.ProgressBar{
			flex: 1;
		}
	}
	&-progressPercent{
		flex: 0 0 3em;
	}
	&-funnels{
		overflow-x: auto;
	}
}
.analytics-page {
	.tab-pane{
		overflow-x: hidden;
	}
}
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
