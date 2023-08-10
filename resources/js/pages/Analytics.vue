<template>
	<div
		v-if="groups"
		class="PageAnalytics"
	>
		<!-- HR -->
		<!-- header -->
		<div class="row my-4">
			<div class="col-3">
				<select
					v-model="currentMonth"
					class="form-control"
				>
					<option
						v-for="(month, index) in $moment.months()"
						:key="month"
						:value="index"
					>
						{{ month }}
					</option>
				</select>
			</div>
			<div class="col-2">
				<select
					v-model="currentYear"
					class="form-control"
				>
					<option
						v-for="year in years"
						:key="year"
						:value="year"
					>
						{{ year }}
					</option>
				</select>
			</div>
			<div class="col-1">
				<JobtronButton
					small
					@click="onRefresh"
				>
					<i class="fa fa-redo-alt" />
				</JobtronButton>
			</div>
			<div class="col-3" />
		</div>

		<!-- tabs -->
		<template>
			<div v-if="hasPremission && currentGroup == 48">
				<b-tabs
					v-model="activeTab"
					type="card"
					content-class="mt-4"
					lazy
				>
					<b-tab
						key="1"
						card
					>
						<template #title>
							<b class="roman">I</b> Рекрутинг
						</template>
						<b-tabs
							type="card"
							lazy
						>
							<b-tab title="Сводная">
								<TabPivot
									:year="currentYear"
									:month="currentMonth"
									:refresh="refresh"
								/>
							</b-tab>
							<b-tab title="Стажеры">
								<TabInterns
									:year="currentYear"
									:month="currentMonth"
									:refresh="refresh"
								/>
							</b-tab>
						</b-tabs>
					</b-tab>
					<b-tab
						key="2"
						card
					>
						<template #title>
							<b class="roman">II</b> Этап стажировки
						</template>
						<TabSecondStage
							:year="currentYear"
							:month="currentMonth"
							:refresh="refresh"
							:groups="groups"
						/>
					</b-tab>
					<b-tab
						key="3"
						card
					>
						<template #title>
							<b class="roman">III</b> Отдел заботы
						</template>
						<!-- Пока пусто -->
					</b-tab>
					<b-tab
						key="4"
						card
					>
						<template #title>
							<b class="roman">IV</b> Увольнение
						</template>
						<TabDismissal
							:year="currentYear"
							:month="currentMonth"
							:refresh="refresh"
						/>
					</b-tab>
					<b-tab
						key="5"
						card
					>
						<template #title>
							<b class="roman">V</b> Маркетинг
						</template>
						<TabMarketing
							:year="currentYear"
							:month="currentMonth"
							:refresh="refresh"
							:months="months"
						/>
					</b-tab>
				</b-tabs>
			</div>
			<div v-else>
				<p>У вас нет доступа к этой группе</p>
			</div>

			<div class="empty-space" />
		</template>
		<Loading
			:active="isLoading"
			:can-cancel="false"
			:is-full-page="true"
		/>
	</div>
</template>

<script>
import { mapState } from 'pinia'
import { useHRStore } from '@/stores/ReportsHR.js'
import { usePortalStore } from '@/stores/Portal'
import { useYearOptions } from '@/composables/yearOptions'

import Loading from 'vue-loading-overlay'
import JobtronButton from '@ui/Button'
import TabPivot from '@/components/pages/Analytics/TabPivot'
import TabInterns from '@/components/pages/Analytics/TabInterns'
import TabSecondStage from '@/components/pages/Analytics/TabSecondStage'
import TabDismissal from '@/components/pages/Analytics/TabDismissal'
import TabMarketing from '@/components/pages/Analytics/TabMarketing'

export default {
	name: 'PageAnalytics',
	components: {
		Loading,
		JobtronButton,
		TabPivot,
		TabInterns,
		TabSecondStage,
		TabDismissal,
		TabMarketing,
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
			currentYear: now.getFullYear(),
			currentMonth: now.getMonth(),
			currentDay: now.getDate(),
			currentGroup: 48,
			refresh: 0,
		}
	},
	watch: {
		groups(){
			this.init()
		},
	},
	computed: {
		...mapState(useHRStore, [
			'isLoading',
			'isReady',
			'error',
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
		hasPremission(){
			return !this.error
		},
	},
	created() {
		if(this.groups){
			this.init()
		}
	},
	methods: {
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
			this.activeTab = parseInt((active == null) ? '1' : active) - 1
		},
		onRefresh(){
			this.refresh++
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

	.tab-pane{
		overflow-x: hidden;
	}

	.btn {
		padding: .375rem .75rem;
		&.btn-sm {
			padding: 0.15rem 0.5rem;
		}
	}
	.pick-panel .btn {
		padding: 1px;
	}
	.b-form-datepicker .btn {
		padding: 0 11px;
		margin: 0;
		margin-right: 5px;
	}

	.cell-input{
		padding: 0 !important;
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
