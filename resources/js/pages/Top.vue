<template>
	<div v-if="data">
		<div class="row">
			<div class="col-3">
				<select
					v-model="monthInfo.currentMonth"
					class="form-control"
					@change="fetchData"
				>
					<option
						v-for="month in $moment.months()"
						:key="month"
						:value="month"
					>
						{{ month }}
					</option>
				</select>
			</div>
			<div class="col-2">
				<select
					v-model="currentYear"
					class="form-control"
					@change="fetchData"
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
				<div
					class="btn btn-primary rounded"
					@click="fetchData()"
				>
					<i class="fa fa-redo-alt" />
				</div>
			</div>
			<div class="col-6" />
		</div>

		<!-- <a href="/timetracking/nps" class="btn link-btn" target="_blank">NPS</a> -->
		<b-tabs
			v-model="activeTab"
			type="card"
			class="mt-4"
		>
			<!-- Полезность -->
			<b-tab
				key="1"
				title="Полезность"
				card
			>
				<div
					class="d-flex flex-column"
					style="margin-bottom: 350px"
				>
					<TopGauges
						:key="ukey"
						:utility_items="activeUtility"
						:editable="true"
						wrapper_class=" br-1"
						page="top"
					/>
				</div>
			</b-tab>

			<!-- Маржа -->
			<b-tab
				key="2"
				title="Маржа"
				card
				@click="showIcons()"
			>
				<TableRentability
					:year="+currentYear"
					:month="+monthInfo.month"
					:rentability-switch="rentabilitySwitch"
				/>
			</b-tab>

			<!-- Выручка -->
			<b-tab
				key="3"
				title="Выручка"
				card
			>
				<div class="table-responsive table-container mt-4">
					<table class="table table-bordered whitespace-no-wrap custom-table-revenue">
						<thead>
							<tr>
								<th
									v-for="(field, findex) in proceeds.fields"
									:key="findex"
									class="t-name table-title"
									:class="{
										'w-295 b-table-sticky-column': findex == 0,
										'w-125': findex == 1,
										'w-80': findex == 2,
										'w-60': findex == 3,
										'text-center': findex != 0,
										'text-left': findex == 0,
									}"
								>
									<template v-if="['+/-'].includes(field)">
										<i
											v-b-popover.hover.right.html="'100% - ( План * Кол-во календарных дней )/ (Итого * Кол-во отработанных дней)'"
											class="fa fa-info-circle"
											title="Опережение плана"
										/>
									</template>
									<template v-if="['%'].includes(field)">
										<i
											v-b-popover.hover.right.html="'( Итого / План ) * 100'"
											class="fa fa-info-circle"
											title="Выполнение плана"
										/>
									</template>
									{{ field }}  <i
										v-if="field == 'Отдел'"
										class="fa fa-plus-square"
										@click="addRow()"
									/>
								</th>
							</tr>
						</thead>
						<tbody>
							<template v-for="(record, rindex) in proceeds.records">
								<tr
									v-if="(proceedsSwitch[record.group_id] && proceedsSwitch[record.group_id].value) || !record.group_id"
									:key="rindex"
								>
									<td
										v-for="(field, findex) in proceeds.fields"
										:key="findex"
										class="t-name table-title"
										:class="{
											'bg-grey': ['w1', 'w2', 'w3', 'w4', 'w5', 'w6'].includes(field),
											'weekend': isWeekend(field),
											'text-left b-table-sticky-column': ['Отдел'].includes(field)
										}"
									>
										<template v-if="!['%', 'План', 'Итого', '+/-', 'Отдел'].includes(field)">
											<div v-if="record['group_id'] < 0">
												<input
													v-model="record[field]"
													type="number"
													class="input"
													@change="updateProceed(record, field, 'day')"
												>
											</div>
											<div v-else>
												<span v-if="record[field] != 0">{{ record[field] }}</span>
												<span v-else />
											</div>
										</template>
										<template v-else>
											<template v-if="field == 'Отдел'">
												<a
													v-if="record['group_id'] >= 0"
													:href="'/timetracking/an?group='+ record['group_id'] + '&active=1&load=1'"
													target="_blank"
												>
													{{ record[field] }}
												</a>
												<div v-else>
													<input
														v-model="record[field]"
														type="text"
														class="input-2"
														@change="updateProceed(record, field, 'name')"
													>
												</div>
												<i
													v-if="record.deleted_at"
													v-b-popover.hover.right.html="'Аналитика архвирована ' + $moment(record.deleted_at, 'YYYY-MM-DD').format('DD.MM.YYYY')"
													class="fa fa-info-circle"
												/>
											</template>
											<template v-else>
												<div>
													{{ record[field] }}
												</div>
											</template>
										</template>
									</td>
								</tr>
							</template>
						</tbody>
					</table>
				</div>
			</b-tab>

			<b-tab
				key="6"
				title=""
				card
			/>
			<b-tab
				key="7"
				title=""
				card
			/>
			<b-tab
				key="8"
				title=""
				card
			/>

			<!-- Прогноз -->
			<b-tab
				key="4"
				title="Прогноз"
				card
			>
				<b-row class="m-0">
					<b-col
						cols="12"
						md="8"
						class="p-0 mt-4"
					>
						<div class="forecast table-container">
							<table class="table table-bordered table-custom-forecast">
								<thead>
									<th class="text-left t-name table-title td-blue">
										Отдел

										<i
											v-b-popover.hover.right.html="'Прогноз по принятию сотрудников на месяц'"
											class="fa fa-info-circle"
											title="Отдел"
										/>
									</th>
									<th class="text-center t-name table-title">
										План

										<i
											v-b-popover.hover.right.html="'Общий план операторов на проект от Заказчика'"
											class="fa fa-info-circle"
											title="План"
										/>
									</th>
									<th class="text-center t-name table-title">
										Факт

										<i
											v-b-popover.hover.right.html="'Фактически работают в группе на должности оператора'"
											class="fa fa-info-circle"
											title="Факт"
										/>
									</th>
									<th class="text-center t-name table-title">
										Осталось принять
									</th>
								</thead>
								<tbody>
									<tr
										v-for="(group, index) in prognoz_groups"
										:key="index"
									>
										<td class="text-left t-name table-title td-blue align-middle">
											{{ group.name }}
										</td>
										<td class="text-center t-name table-title align-middle">
											<input
												v-model="group.plan"
												type="number"
												@change="saveGroupPlan(index)"
											>
										</td>
										<td class="text-center t-name table-title align-middle">
											{{ group.applied }}
										</td>
										<td class="text-center t-name table-title align-middle">
											{{ isNaN(group.left_to_apply) ? 0 : Number(group.left_to_apply) }}
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</b-col>
				</b-row>
			</b-tab>

			<!-- NPS -->
			<b-tab
				key="5"
				title="NPS"
				card
			>
				<NPS
					:activeuserid="+activeuserid"
					:show_header="false"
				/>
			</b-tab>

			<template #tabs-end>
				<JobtronButton
					v-if="activeTab < 3"
					small
					secondary
					class="ml-a"
					@click="isArchiveOpen = true"
				>
					<i class="icon-nd-settings" />
				</JobtronButton>
			</template>
		</b-tabs>

		<SideBar
			title="Активные спидометры"
			width="35%"
			:open="isArchiveOpen"
			@close="isArchiveOpen = false"
		>
			<TopSwitches
				:items="switches"
				@change="onChangeSwitch"
			/>
		</SideBar>

		<div class="empty-space" />
	</div>
</template>

<script>
/* eslint-disable camelcase */

import { mapState } from 'pinia'
import { usePortalStore } from '@/stores/Portal'
import { useYearOptions } from '@/composables/yearOptions'
import {
	fetchTop,
	fetchArchiveUtility,
	fetchArchiveRentability,
	fetchArchiveProceeds,
	switchArchiveTop,
} from '@/stores/api'

const TopGauges = () => import(/* webpackChunkName: "TopGauges" */ '@/components/TopGauges')  // TOП спидометры, есть и в аналитике
import TableRentability from '@/components/tables/TableRentability' // ТОП рентабельность
import NPS from '@/components/tables/NPS' // Оценка руководителей
import TopSwitches from '@/components/pages/Top/TopSwitches'
import JobtronButton from '@ui/Button'
import SideBar from '@ui/Sidebar'

export default {
	name: 'PageTop',
	components: {
		TopGauges,
		TableRentability,
		NPS,
		TopSwitches,
		JobtronButton,
		SideBar,
	},
	props: {
		data: {
			type: Object,
			default: null
		},
	},
	data() {
		const now = new Date()
		return {
			activeuserid: this.$laravel.userId,
			afterCreated: false,
			activeTab: 0,
			utility: [], // первая вкладка
			rentability: [], // вторая
			proceeds: [], // третья

			utilitySwitch: {},
			rentabilitySwitch: {},
			proceedsSwitch: {},

			prognoz_groups: [], //
			currentYear: now.getFullYear(),
			monthInfo: {
				currentMonth: null,
				monthEnd: 0,
				workDays: 0,
				weekDays: 0,
				daysInMonth: 0,
				month: now.getMonth() + 1
			},
			gaugeOptions: {
				angle: 0,
				staticLabels: {
					font: '9px sans-serif', // Specifies font
					labels: [0, 50, 80, 100, 120], // Print labels at these values
					color: '#000000', // Optional: Label text color
					fractionDigits: 0, // Optional: Numerical precision. 0=round off.
				},
				staticZones: [
					{strokeStyle: '#F03E3E', min: 0, max: 49}, // Red
					{strokeStyle: '#fd7e14', min: 50, max: 79}, // Orange
					{strokeStyle: '#FFDD00', min: 80, max: 90}, // Yellow
					{strokeStyle: '#30B32D', min: 91, max: 120}, // Green
				],
				pointer: {
					length: 0.5, // // Relative to gauge radius
					strokeWidth: 0.025, // The thickness
					color: '#000000', // Fill color
				},
				limitMax: true,
				limitMin: true,
				lineWidth: 0.2,
				radiusScale: 0.8,
				colorStart: '#6FADCF',
				generateGradient: true,
				highDpiSupport: true,
			},
			ukey: 1,
			isArchiveOpen: false,
		}
	},
	computed: {
		...mapState(usePortalStore, ['portal']),
		years(){
			if(!this.portal.created_at) return [new Date().getFullYear()]
			return useYearOptions(new Date(this.portal.created_at).getFullYear())
		},
		activeUtility(){
			return this.utility.filter(util => this.utilitySwitch[util.id] && this.utilitySwitch[util.id].value)
		},
		archiveUtility(){
			return this.utility.filter(util => !(this.utilitySwitch[util.id] && this.utilitySwitch[util.id].value))
		},
		activeRentability(){
			return this.rentability.filter(rent => this.isActiveRentability(rent.group_id))
		},
		switches(){
			switch(this.activeTab){
			case 1:
				return this.rentabilitySwitch
			case 2:
				return this.proceedsSwitch
			}
			return this.utilitySwitch
		}
	},
	watch: {
		data(){
			this.init()
		}
	},
	created() {
		if(this.data){
			this.init()
		}
	},
	methods: {
		init(){
			this.utility = this.data.utility;
			this.proceeds = this.data.proceeds;
			this.prognoz_groups = this.data.prognoz_groups
			this.setMonth()
			this.fetchData()
			this.fetchSwitches()
		},
		showIcons(){
			this.rentability = this.data.rentability || [];
		},
		setMonth() {
			this.monthInfo.currentMonth = this.monthInfo.currentMonth ? this.monthInfo.currentMonth : this.$moment().format('MMMM')
			this.monthInfo.month = this.monthInfo.currentMonth ? this.$moment(this.monthInfo.currentMonth, 'MMMM').format('M') : this.$moment().format('M')
			let currentMonth = this.$moment(this.monthInfo.currentMonth, 'MMMM')
			//Расчет выходных дней
			this.monthInfo.monthEnd = currentMonth.endOf('month'); //Конец месяца
			this.monthInfo.weekDays = currentMonth.weekdayCalc(currentMonth.startOf('month').toString(), currentMonth.endOf('month').toString(), [6]) //Колличество выходных
			this.monthInfo.daysInMonth = new Date(this.currentYear, this.$moment(this.monthInfo.currentMonth, 'MMMM').format('M'), 0).getDate() //Колличество дней в месяце
			this.monthInfo.workDays = this.monthInfo.daysInMonth - this.monthInfo.weekDays //Колличество рабочих дней
		},

		async fetchData() {
			const loader = this.$loading.show()

			try {
				const {
					rentability,
					utility,
					proceeds,
				} = await fetchTop({
					month: this.$moment(this.monthInfo.currentMonth, 'MMMM').format('M'),
					year: this.currentYear,
				})

				this.setMonth()

				if(this.afterCreated) this.rentability = rentability || []
				this.afterCreated = true;
				this.utility = utility;
				this.proceeds = proceeds;

				this.ukey++;
			}
			catch (error) {
				alert(error)
			}

			loader.hide()
		},

		async fetchSwitches(){
			try {
				const { data: utility } = await fetchArchiveUtility()
				this.utilitySwitch = utility.reduce((result, group) => {
					result[group.id] = {
						...group,
						value: !!group.switch
					}
					return result
				}, {})

				const { data: rentability } = await fetchArchiveRentability()
				this.rentabilitySwitch = rentability.reduce((result, group) => {
					result[group.id] = {
						...group,
						value: !!group.switch
					}
					return result
				}, {})

				const { data: proceeds } = await fetchArchiveProceeds()
				this.proceedsSwitch = proceeds.reduce((result, group) => {
					result[group.id] = {
						...group,
						value: !!group.switch
					}
					return result
				}, {})
			}
			catch (error) {
				console.error('[fetchSwitches]', error)
			}
		},

		isWeekend(field) {
			var arr = field.split('.');
			var month = Number(arr[1]) - 1;
			var dayOfWeek = new Date(this.currentYear, month, arr[0]).getDay();

			return dayOfWeek == 6 || dayOfWeek == 0;
		},

		saveRentGauge(g_index) {
			let loader = this.$loading.show();
			this.axios.post('/timetracking/top/save_rent_max', {
				gauge: this.rentability[g_index],
			})
				.then(() => {
					this.$toast.success('Успешно сохранено!')
					this.rentability[g_index].editable = false
					this.fetchData()
					loader.hide()
				}).catch(error => {
					alert(error)
					loader.hide()
				});
		},

		saveGroupPlan(index) {
			const loader = this.$loading.show();
			const prognozGroup = this.prognoz_groups[index]
			this.axios.post('/timetracking/top/save_group_plan', {
				group_id: prognozGroup.id,
				plan: prognozGroup.plan,
			}).then(() => {
				this.$toast.success('Успешно сохранено!')
				prognozGroup.left_to_apply = Number(prognozGroup.plan || 0) - Number(prognozGroup.applied || 0);
				loader.hide()
			}).catch(error => {
				alert(error)
				loader.hide()
			});
		},

		updateProceed(record, field, type) {
			let loader = this.$loading.show();

			this.axios.post('/timetracking/top/proceeds/update', {
				group_id: record['group_id'],
				value: record[field],
				date: field == 'Отдел' ?  this.proceeds.fields[5] : field,
				name: record['Отдел'],
				type: type,
				year: this.currentYear,
			})
				.then(() => {
					this.$toast.success('Успешно сохранено!');
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
		},

		isActiveRentability(groupId){
			return this.rentabilitySwitch[groupId] && this.rentabilitySwitch[groupId].value
		},
		onChangeSwitch({id, value}){
			const switch_column = ['switch_utility', 'switch_rentability', 'switch_proceeds'][this.activeTab]
			switchArchiveTop({
				id,
				switch_column,
				switch_value: value ? 1 : 0
			})
			const name = ['utilitySwitch', 'rentabilitySwitch', 'proceedsSwitch'][this.activeTab]
			const item = this[name][id]
			if(item){
				item.value = !item.value
				item.switch = item.value ? 1 : 0
			}
		}
	}
}
</script>

<style lang="scss">
.table-custom-forecast {
	table-layout: fixed;
	width: auto;

	.td-blue {
		background-color: #DDE9FF;
		border: 1px solid #bdcff1 !important;
	}

	thead {
		th {
			width: 20%;
			max-width: 20%;

			&:first-child {
				width: 40%;
				max-width: 40%;
			}
		}
	}

	tbody {
		td {
			input {
				width: 100%;
			}
		}
	}
}

.weekend {
	background-color: #fef2cb !important;
}

.gauge-block {
	margin-right: 10px;
	margin-top: 10px;
}
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

.gauge {
	cursor: pointer;
	&:last-child {
		border-bottom: none;
	}
	&:hover{
		.fa-cog {
			display: block;
		}
	}
}

.br-1 {
	border-right: 1px solid #f3f3f3;
	border-bottom: 1px solid #f3f3f3;
}

.text-20 {
	font-size: 20px;
}

table.tops{
	th:first-child,
	td:first-child {
		background: #ebedf5;
		font-weight: bold;
		min-width: 200px;
		text-align: left !important;
		position: sticky;
		left: 0;
		border-right: 2px solid #a1b7cc !important;
		border-left: 2px solid #a1b7cc !important;
	}

	thead{
		td,
		th{
			border-left: 1px solid #cccccc!important;
		}
	}
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
