<template>
	<div class="t-stats">
		<table class="j-table">
			<thead>
				<tr class="table-heading">
					<th class="first-column" />
					<th class="w">
						KPI
					</th>
					<th
						v-if="editable"
						class="px-2"
					>
						Средний %
					</th>
					<th
						v-if="!editable"
						class="px-2"
					>
						Нижний порог отсчета
					</th>
					<th
						v-if="!editable"
						class="px-2"
					>
						Верхний порог отсчета
					</th>
					<th
						v-if="!editable"
						class="px-2"
					>
						При выполнении на 80-99%
					</th>
					<th
						v-if="!editable"
						class="px-2"
					>
						При выполнении на 100%
					</th>
					<th
						v-if="!editable"
						class="px-2"
					>
						Заработано
					</th>
					<th
						v-if="editable"
						class="px-2"
					>
						Средний %
					</th>
				</tr>
			</thead>
			<tbody>
				<template v-for="(wrap_item, w) in items.slice().reverse()">
					<template v-if="searchText.length == 0 || (wrap_item.target && wrap_item.target.name.includes(searchText))">
						<tr
							:key="w"
							class="main-row"
						>
							<td
								class="pointer p-2 text-center"
								@click="wrap_item.expanded = !wrap_item.expanded"
							>
								<div class="d-flex align-items-center justify-content-center px-2">
									<span class="mr-2">{{ w + 1 }}</span>
									<i
										v-if="wrap_item.expanded"
										class="fa fa-minus mt-1"
									/>
									<i
										v-else
										class="fa fa-plus mt-1"
									/>
								</div>
							</td>
							<td class="p-4">
								<span v-if="wrap_item.target != null">{{ wrap_item.target.name }}</span>
								<span v-else>---</span>
							</td>
							<td
								v-if="editable"
								class="p-4"
							>
								{{ wrap_item.avg }}%
							</td>
							<td
								v-if="!editable"
								class="p-4"
							>
								{{ wrap_item.lower_limit }}%
							</td>
							<td
								v-if="!editable"
								class="p-4"
							>
								{{ wrap_item.upper_limit }}%
							</td>
							<td
								v-if="!editable"
								class="p-4"
							>
								{{ wrap_item.users.length > 0 && wrap_item.users[0].full_time == 1 ? wrap_item.completed_80 : wrap_item.completed_80 / 2 }}
							</td>
							<td
								v-if="!editable"
								class="p-4"
							>
								{{ wrap_item.users.length > 0 && wrap_item.users[0].full_time == 1 ? wrap_item.completed_100 : wrap_item.completed_100 / 2 }}
							</td>
							<td
								v-if="!editable"
								class="p-4"
							>
								{{ wrap_item.my_sum }}
							</td>
							<td v-if="editable" />
						</tr>
						<template v-if="wrap_item.users != undefined && wrap_item.users.length > 0">
							<tr
								:key="w + 'a'"
								class="collapsable"
								:class="{'active': wrap_item.expanded || !editable }"
							>
								<td :colspan="editable ? 4 : 7">
									<div class="table__wrapper w-100">
										<table class="child-table">
											<template v-for="(user, i) in wrap_item.users">
												<tr
													v-if="editable"
													:key="i"
													class="child-row"
												>
													<td
														class="pointer p-2 text-center"
														@click="user.expanded = !user.expanded"
													>
														<div class="d-flex align-center justify-content-center px-2">
															<span class="mr-2 bg-transparent">{{ i + 1 }}</span>
															<i
																v-if="user.expanded"
																class="fa fa-minus mt-1"
															/>
															<i
																v-else
																class="fa fa-plus mt-1"
															/>
														</div>
													</td>
													<td class="p-4">
														{{ user.name }}
													</td>
													<template v-if="user.items !== undefined">
														<td
															v-for="(kpi_item, index) in user.items"
															:key="index"
															class="px-2"
														>
															{{ kpi_item.name }} <b>{{ kpi_item.percent }}%</b>
														</td>
													</template>
												</tr>
												<template v-if="user.items !== undefined">
													<tr
														:key="i + 'a'"
														class="collapsable"
														:class="{'active': user.expanded}"
													>
														<td :colspan="fields.length + 2">
															<div class="table__wrapper__second w-100">
																<KpiItems
																	:my_sum="user.full_time == 1 ? wrap_item.completed_100 : wrap_item.completed_100 / 2"
																	:kpi_id="user.id"
																	:items="user.items"
																	:expanded="user.expanded"
																	:activities="activities"
																	:groups="groups"
																	:completed_80="wrap_item.completed_80"
																	:completed_100="wrap_item.completed_100"
																	:lower_limit="wrap_item.lower_limit"
																	:upper_limit="wrap_item.upper_limit"
																	:editable="editable"
																	:kpi_page="false"
																	:date="date"
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
					</template>
				</template>
			</tbody>
		</table>
	</div>
</template>

<script>
/* eslint-disable vue/no-mutating-props */
import KpiItems from '@/pages/kpi/KpiItems'
/* eslint-disable-next-line camelcase */
import {kpi_fields} from './kpis.js'

export default {
	name: 'StatsTable',
	components: {
		KpiItems,
	},
	props: {
		searchText: {
			type: String,
			default: '',
		},
		items: {
			type: Array,
			default: () => [],
		},
		activities: {
			type: Array,
			default: () => [],
		},
		groups: {
			type: Array,
			default: () => [],
		},
		editable: {
			type: Boolean,
			default: false
		},
		date: {
			type: Object,
			default: null
		}
	},

	data() {
		return {
			showFields: [],
			fields: [],
		}
	},

	watch: {
		showFields: {
			handler: function (val) {
				/* eslint-disable-next-line camelcase */
				localStorage.kpi_show_fields = JSON.stringify(val);
				this.prepareFields();
			},
			deep: true
		},
	},

	created() {
		this.prepareFields()
		this.countAvg();
	},
	mounted(){
	},
	methods: {

		expand(w) {
			this.items[w].expanded = !this.items[w].expanded
		},

		prepareFields() {
			const visibleFields = []

			/* eslint-disable-next-line camelcase */
			kpi_fields.forEach(field => {
				if(this.showFields[field.key] != undefined
					&& this.showFields[field.key]
				) {
					visibleFields.push(field)
				}
			});

			/* eslint-disable-next-line camelcase */
			this.fields = kpi_fields;
		},

		countAvg() {

			this.items.forEach(kpi => {

				let kpiSum = 0;
				let kpiCount = 0;

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
					kpiSum += Number(avg);
					kpiCount++;
				});

				/**
				* count avg completed percent of kpi by users
				*/
				kpi.avg = kpiCount > 0 ? Number(Number(kpiSum / kpiCount * 100).toFixed(2)) : 0;

			});
		},

	}
}
</script>


<style scoped lang="scss">
.child-table {width:100%}
.profile-salary-info {
	.table td, .table th, .table thead th {
		vertical-align: middle;
		min-width: 42px;
		font-size: 13px;
		padding: 5px 12px;
		text-align: center;
	}

	.j-table .table-inner {
		background: #e9eef3;
	}
}
</style>
