<template>
	<div v-if="data">
		<div class="row">
			<div class="col-3">
				<select
					class="form-control"
					v-model="monthInfo.currentMonth"
					@change="fetchData"
				>
					<option
						v-for="month in $moment.months()"
						:value="month"
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
					@change="fetchData"
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
					@click="fetchData()"
				>
					<i class="fa fa-redo-alt" />
				</div>
			</div>
			<div class="col-6" />
		</div>

		<!-- <a href="/timetracking/nps" class="btn link-btn" target="_blank">NPS</a> -->
		<b-tabs
			type="card"
			class="mt-4"
			default-active-key="1"
		>
			<b-tab
				title="Полезность"
				key="1"
				card
			>
				<div
					class="d-flex flex-column"
					style="margin-bottom: 350px"
				>
					<TopGauges
						:utility_items="activeUtility"
						:editable="true"
						wrapper_class="  br-1"
						:key="ukey"
						page="top"
						@archive="onArchiveUtility"
					/>
					<div class="d-flex jcfe">
						<JobtronButton
							v-if="archiveUtilityWithGauges.length"
							@click="isArchiveOpen = true"
							title="Открыть архив"
						>
							Архив
						</JobtronButton>
					</div>
				</div>
			</b-tab>

			<b-tab
				title="Рентабельность операторов"
				key="2"
				@click="showIcons()"
				card
			>
				<div
					class="d-flex flex-wrap mb-5"
					:key="ukey"
				>
					<template v-for="(gauge, g_index) in rentability">
						<div
							v-if="isActiveRentability(gauge.group_id)"
							:key="gauge.name"
							class="gauge-block"
						>
							<div @click="gauge.editable = !gauge.editable">
								<VGauge
									:value="gauge.value"
									unit="%"
									:options="gauge.options"
									:max-value="Number(gauge.max_value)"
									:top="true"
									height="75px"
									width="125px"
									gauge-value-class="gauge-span"
								/>
							</div>
							<p
								class="text-center font-bold"
								style="font-size: 14px;margin-bottom: 0;"
							>
								<a
									:href="'/timetracking/an?group='+ gauge.group_id + '&active=1&load=1'"
									target="_blank"
								>{{ gauge.name }}</a>
								<span
									class=" ml-2 pointer"
									title="Отправить в архив"
									@click="onArchiveUtility(gauge.group_id)"
								>
									<i class="fa fa-trash" />
								</span>
							</p>
							<p class="text-center font-bold text-14">
								{{ gauge.value }}%
							</p>
							<div
								v-if="gauge.editable"
								class="mb-5 edt-window"
								style="width: 125px;"
							>
								<div>
									<div class="d-flex justify-content-between align-items-center">
										<span class="pr-2 l-label">Max</span>
										<input
											type="text"
											class="form-control form-control-sm w-250 wiwi"
											v-model="gauge.max_value"
										>
									</div>
								</div>
								<div class="d-flex">
									<button
										@click="saveRentGauge(g_index)"
										class="btn btn-primary btn-sm rounded mt-1 mr-2"
									>
										Сохранить
									</button>
								</div>
							</div>
						</div>
					</template>

					<div class="ml-a pt-4">
						<JobtronButton
							v-if="archiveUtility.length"
							@click="isArchiveOpen = true"
							title="Открыть архив"
						>
							Архив
						</JobtronButton>
					</div>
				</div>


				<TableRentability
					:year="+currentYear"
					:month="+monthInfo.month"
				/>
			</b-tab>


			<b-tab
				title="Выручка"
				key="3"
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
											class="fa fa-info-circle"
											v-b-popover.hover.right.html="'100% - ( План * Кол-во календарных дней )/ (Итого * Кол-во отработанных дней)'"
											title="Опережение плана"
										/>
									</template>
									<template v-if="['%'].includes(field)">
										<i
											class="fa fa-info-circle"
											v-b-popover.hover.right.html="'( Итого / План ) * 100'"
											title="Выполнение плана"
										/>
									</template>
									{{ field }}  <i
										class="fa fa-plus-square"
										v-if="field == 'Отдел'"
										@click="addRow()"
									/>
								</th>
							</tr>
						</thead>
						<tbody>
							<tr
								v-for="(record, rindex) in proceeds.records"
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
												type="number"
												class="input"
												v-model="record[field]"
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
													type="text"
													class="input-2"
													v-model="record[field]"
													@change="updateProceed(record, field, 'name')"
												>
											</div>
										</template>

										<template v-else>
											<div>
												{{ record[field] }}
											</div>
										</template>
									</template>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</b-tab>
			<b-tab
				title=""
				key="6"
				card
			/>
			<b-tab
				title=""
				key="7"
				card
			/>
			<b-tab
				title=""
				key="8"
				card
			/>

			<b-tab
				title="Прогноз"
				key="4"
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
											class="fa fa-info-circle"
											v-b-popover.hover.right.html="'Прогноз по принятию сотрудников на месяц'"
											title="Отдел"
										/>
									</th>
									<th class="text-center t-name table-title">
										План

										<i
											class="fa fa-info-circle"
											v-b-popover.hover.right.html="'Общий план операторов на проект от Заказчика'"
											title="План"
										/>
									</th>
									<th class="text-center t-name table-title">
										Факт

										<i
											class="fa fa-info-circle"
											v-b-popover.hover.right.html="'Фактически работают в группе на должности оператора'"
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
												type="number"
												v-model="group.plan"
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

			<b-tab
				title="NPS"
				key="5"
				card
			>
				<NPS
					:activeuserid="+activeuserid"
					:show_header="false"
				/>
			</b-tab>
		</b-tabs>

		<SideBar
			title="Архив"
			width="35%"
			:open="isArchiveOpen"
			@close="isArchiveOpen = false"
			class="TopArchive"
		>
			<div
				v-for="util in archiveUtility"
				:key="util.id"
				class="TopArchive-item"
			>
				<div class="TopArchive-title">
					{{ util.name }}
				</div>
				<i
					class="fa fa-trash-restore TopArchive-button"
					title="Восстановить из архива"
					@click="onRestoreUtility(util.id)"
				/>
			</div>
		</SideBar>

		<div class="empty-space" />
	</div>
</template>

<script>
import { mapState } from 'pinia'
import { usePortalStore } from '@/stores/Portal'
const VGauge = () => import(/* webpackChunkName: "TopGauges" */ 'vgauge')
const TopGauges = () => import(/* webpackChunkName: "TopGauges" */ '@/components/TopGauges')  // TOП спидометры, есть и в аналитике
import TableRentability from '@/components/tables/TableRentability' // ТОП рентабельность
import NPS from '@/components/tables/NPS' // Оценка руководителей
import { useYearOptions } from '@/composables/yearOptions'
import JobtronButton from '@ui/Button'
import SideBar from '@ui/Sidebar'
import { topArchiveUtility } from '@/stores/api'

export default {
	name: 'PageTop',
	components: {
		TopGauges,
		VGauge,
		TableRentability,
		NPS,
		JobtronButton,
		SideBar,
	},
	props: ['data', 'activeuserid'],
	data() {
		const now = new Date()
		return {
			afterCreated: false,
			rentability: [], // первая вкладка
			utility: [], // вторая
			proceeds: [], // третья
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
	watch: {
		data(){
			this.init()
		}
	},
	computed: {
		...mapState(usePortalStore, ['portal']),
		years(){
			if(!this.portal.created_at) return [new Date().getFullYear()]
			return useYearOptions(new Date(this.portal.created_at).getFullYear())
		},
		activeUtility(){
			return this.utility.filter(util => !util.archive_utility)
		},
		archiveUtility(){
			return this.utility.filter(util => util.archive_utility)
		},
		archiveUtilityWithGauges(){
			return this.archiveUtility.filter(util => util.gauges.length)
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
		},
		showIcons(){
			this.rentability = this.data.rentability;
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

		fetchData() {
			let loader = this.$loading.show();

			this.axios.post('/timetracking/top', {
				month: this.$moment(this.monthInfo.currentMonth, 'MMMM').format('M'),
				year: this.currentYear,
			}).then(response => {

				this.setMonth()
				if(this.afterCreated){
					this.rentability = response.data.rentability;
				}
				this.afterCreated = true;
				this.utility = response.data.utility;
				this.proceeds = response.data.proceeds;

				this.ukey++;

				loader.hide()
			}).catch(error => {
				loader.hide()
				alert(error)
			});
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
				gauge: this.rentability[g_index]
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
			let loader = this.$loading.show();
			this.axios.post('/timetracking/top/save_group_plan', {
				group_id: this.prognoz_groups[index].id,
				plan: this.prognoz_groups[index].plan,
			}).then(() => {

				this.$toast.success('Успешно сохранено!')
				this.prognoz_groups[index].left_to_apply = Number(this.prognoz_groups[index].plan) - Number(this.prognoz_groups[index].fired);
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

		async onArchiveUtility(groupId){
			if(!confirm('Убрать полезность в архив?')) return
			try{
				const {message} = await topArchiveUtility({
					group_id: groupId,
					is_archive: true,
				})
				if(message === 'Success'){
					const utility = this.utility.find(util => util.id === groupId)
					if(utility){
						utility.archive_utility = true
						this.$forceUpdate()
					}
				}
			}
			catch{
				this.$toast.error('Не удалось отправить полезность в архив')
			}
		},

		async onRestoreUtility(groupId){
			if(!confirm('Восстановить полезность?')) return
			try{
				const {message} = await topArchiveUtility({
					group_id: groupId,
					is_archive: false,
				})
				if(message === 'Success'){
					const utility = this.utility.find(util => util.id === groupId)
					if(utility){
						utility.archive_utility = false
						this.$forceUpdate()
						if(!this.archiveUtility.length) this.isArchiveOpen = false
					}
				}
			}
			catch{
				this.$toast.error('Не удалось восстановить полезность')
			}
		},

		isActiveRentability(groupId){
			const utility = this.utility.find(util => util.id === groupId)
			if(!utility) return true
			return !utility.archive_utility
		},
	}
}
</script>

<style lang="scss">
	.TopArchive{
		&.ui-sidebar{
			&.is-open{
				.ui-sidebar__body{
					right: 60px;
				}
			}
		}

		&-item{
			display: flex;
			align-items: center;
			gap: 10px;

			padding: 10px;
			&:hover{
				background-color: #DDE9FF;
			}
		}
		&-title{
			flex: 1;
			white-space: nowrap;
			overflow: hidden;
			text-overflow: ellipsis;
		}
		&-button{
			cursor: pointer;
		}
	}
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

table.tops th:first-child,
table.tops td:first-child {
  background: #ebedf5;
  font-weight: bold;
  min-width: 200px;
  text-align: left !important;
  position: sticky;
  left: 0;
  border-right: 2px solid #a1b7cc !important;
  border-left: 2px solid #a1b7cc !important;
}

table.tops thead td,
table.tops thead th{
  border-left: 1px solid #cccccc!important;
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
