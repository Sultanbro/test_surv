<template>
	<div
		class="popup__content mt-3"
		:class="{'v-loading': loading}"
	>
		<div class="popup__filter pb-4">
			<DateSelect
				v-model="selectedDate"
				class="ml-a Balance-datePicker"
			/>
		</div>

		<div class="kpi__content">
			<div class="tabs">
				<div class="tabs__wrapper custom-scroll">
					<div
						v-for="(wrap_item, w) in items.slice().reverse()"
						:key="w"
						onclick="switchTabs(this)"
						:data-index="w"
						class="tab__item"
						:class="{'is-active': w == 0}"
					>
						{{ wrap_item.target === null ? '---' : wrap_item.target.name }}
					</div>
				</div>
				<div class="tab__content">
					<div
						v-for="(wrap_item, w) in items.slice().reverse()"
						:key="w"
						:data-content="w"
						class="tab__content-item"
						:class="{'is-active': w == 0}"
					>
						<div class="kpi__kaspi">
							<div class="kpi__kaspi-wrapper">
								<div class="kpi__kaspi-left">
									<table>
										<tr>
											<td class="blue">
												Выполнение KPI от 80-99%
											</td>
											<td>{{ wrap_item.users.length > 0 && wrap_item.users[0].full_time == 1 ? wrap_item.completed_80 : wrap_item.completed_80 / 2 }}</td>
										</tr>
										<tr>
											<td class="blue">
												Выполнение KPI на 100%
											</td>
											<td>{{ wrap_item.users.length > 0 && wrap_item.users[0].full_time == 1 ? wrap_item.completed_100 : wrap_item.completed_100 / 2 }}</td>
										</tr>
									</table>
								</div>
								<div class="kpi__kaspi-right">
									<table>
										<thead>
											<tr>
												<th>Нижний порог отсечения премии, %</th>
												<th>Верхний порог отсечения премии, %</th>
											</tr>
										</thead>
										<tr>
											<td>{{ wrap_item.lower_limit }}</td>
											<td>{{ wrap_item.upper_limit }}</td>
										</tr>
									</table>
								</div>
							</div>
						</div>

						<div class="kpi__activities">
							<div class="kpi__title popup__content-title">
								Активности KPI
							</div>



							<table class="kpi__activities-table">
								<template v-if="wrap_item.users != undefined && wrap_item.users.length > 0">
									<tr
										class="collapsable"
										:class="{'active': wrap_item.expanded || !editable }"
										:key="w + 'a'"
									>
										<td
											:colspan="editable ? 3 : 7"
											class="kpi__activities-outer"
										>
											<div class="table__wrapper">
												<table class="child-table">
													<template v-for="(user, i) in wrap_item.users">
														<tr
															v-if="editable"
															:key="i"
															class="child-row"
														>
															<td
																@click="user.expanded = !user.expanded"
																class="pointer px-2"
															>
																<span class="ml-2 bg-transparent">{{ i + 1 }}</span>
															</td>
															<td class="px-2 py-1">
																{{ user.name }}
															</td>

															<template v-if="user.items !== undefined">
																<td
																	v-for="kpi_item in user.items"
																	class="px-2"
																	:key="kpi_item"
																>
																	{{ kpi_item.name }} <b>{{ kpi_item.percent }}%</b>
																</td>
															</template>
														</tr>

														<template v-if="user.items !== undefined">
															<tr
																class="collapsable"
																:class="{'active': true}"
																:key="i + 'a'"
															>
																<td
																	:colspan="fields.length + 2"
																	class="kpi__activities-outer"
																>
																	<div class="table__wrapper__second">
																		<KpiItemsV2
																			:my_sum="user.full_time == 1 ? wrap_item.completed_100 : wrap_item.completed_100 / 2"
																			:kpi_id="user.id"
																			:items="user.items"
																			:expanded="true"
																			:activities="activities"
																			:groups="groups"
																			:completed_80="wrap_item.completed_80"
																			:completed_100="wrap_item.completed_100"
																			:lower_limit="wrap_item.lower_limit"
																			:upper_limit="wrap_item.upper_limit"
																			:editable="false"
																			:kpi_page="false"
																			date="date"
																			@getSum="wrap_item.my_sum = $event"
																			@recalced="countAvg"
																		/>
																	</div>
																</td>
															</tr>
														</template>
													</template>
												</table>
											</div>
										</td>
									</tr>
								</template>
							</table>


							<div class="kpi__activities-tip">
								* сумма премии за выполнение показателей начнет меняться после достижения 80% от целевого значения на месяц
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import { mapState } from 'pinia'
import { usePortalStore } from '@/stores/Portal'
import {kpi_fields, parseKPI} from '../../kpi/kpis.js';
import KpiItemsV2 from '@/pages/kpi/KpiItemsV2.vue'
import { useYearOptions } from '@/composables/yearOptions'
import DateSelect from '../DateSelect'

export default {
	name: 'PopupKpi',
	components: {
		KpiItemsV2,
		DateSelect,
	},
	props: {},
	data: function () {
		return {
			groups: [],
			editable: false,
			activities: [],
			items: [],
			dateInfo: {
				currentMonth: null,
				monthEnd: 0,
				workDays: 0,
				weekDays: 0,
				daysInMonth: 0
			},
			show_fields: [],
			all_fields: kpi_fields,
			fields: [],
			non_editable_fields: [
				'created_at',
				'updated_at',
				'created_by',
				'updated_by',
			],
			user_id: 1,
			loading: false,
			selectedDate: this.$moment().format('DD.MM.YYYY'),
		};
	},
	computed: {
		...mapState(usePortalStore, ['portal']),
		currentMonth(){
			return this.$moment(this.selectedDate, 'DD.MM.YYYY').format('MMMM')
		},
		currentYear(){
			return this.$moment(this.selectedDate, 'DD.MM.YYYY').format('YYYY')
		},
		years(){
			if(!this.portal.created_at) return [new Date().getFullYear()]
			return useYearOptions(new Date(this.portal.created_at).getFullYear())
		},
	},
	watch: {
		selectedDate(){
			this.$nextTick(() => {
				this.fetchBefore()
			})
		}
	},
	created(){
		this.setMonth()
		this.prepareFields()
		this.fetchBefore()
	},
	methods: {
		/**
		 * set month
		 */
		setMonth() {
			let year = this.$moment().format('YYYY')
			this.dateInfo.currentMonth = this.dateInfo.currentMonth ? this.dateInfo.currentMonth : this.$moment().format('MMMM')
			this.currentMonth = this.dateInfo.currentMonth;
			this.dateInfo.date = `${this.dateInfo.currentMonth} ${year}`

			let currentMonth = this.$moment(this.dateInfo.currentMonth, 'MMMM')

			//Расчет выходных дней
			this.dateInfo.monthEnd = currentMonth.endOf('month'); //Конец месяца
			this.dateInfo.weekDays = currentMonth.weekdayCalc(this.dateInfo.monthEnd, [6]) //Колличество выходных
			this.dateInfo.daysInMonth = currentMonth.daysInMonth() //Колличество дней в месяце
			this.dateInfo.workDays = this.dateInfo.daysInMonth - this.dateInfo.weekDays //Колличество рабочих дней
		},

		fetchBefore() {
			this.fetchData({
				data_from: {
					year: this.currentYear,
					month: this.$moment(this.currentMonth, 'MMMM').format('M')
				},
				user_id: this.$laravel.userId
			})
		},

		fetchData(filters = null) {
			this.loading = true

			this.axios.post('/statistics/kpi', {
				filters: filters
			}).then(({data}) => {

				// items
				this.items = data.items.map(res=> ({...parseKPI(res), my_sum: 0}))

				this.activities = data.activities;
				this.groups = data.groups;

				this.loading = false
			}).catch(error => {
				this.loading = false
				alert(error)
			});
		},

		countAvg() {

			this.items.forEach(kpi => {

				let kpi_sum = 0;
				let kpi_count = 0;

				kpi.users.forEach(user => {

					let count = 0;
					let sum = 0;
					let avg = 0;

					user.items.forEach(item => {
						sum += Number(item.percent);
						count++;
					});

					/**
					 * count avg of user items
					 */
					avg = count > 0 ? Number(sum / count).toFixed(2) : 0;

					user.avg = avg;

					// all kpi sum
					kpi_sum += Number(avg);
					kpi_count++;
				});

				/**
				 * count avg completed percent of kpi by users
				 */
				kpi.avg = kpi_count > 0 ? Number(Number(kpi_sum / kpi_count * 100).toFixed(2)) : 0;

			});
		},

		prepareFields() {
			let visible_fields = [] // ??????

			kpi_fields.forEach(field => {
				if(this.show_fields[field.key]) {
					visible_fields.push(field)
				}
			});

			this.fields = kpi_fields;
		},
	}
};
</script>
