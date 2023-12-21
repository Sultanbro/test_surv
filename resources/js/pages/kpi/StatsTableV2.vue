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
				<template v-for="(wrap_item, w) in reversedItems">
					<tr
						:key="w"
						class="main-row"
					>
						<td
							class="pointer p-2 text-center"
							@click="kpis[types[wrap_item.targetable_type]][wrap_item.targetable_id] ? closeKPI(wrap_item.targetable_id, wrap_item.targetable_type) : fetchKPI(wrap_item.targetable_id, wrap_item.targetable_type)"
						>
							<div class="d-flex align-items-center justify-content-center px-2">
								<span class="mr-2">{{ w + 1 }}</span>
								<i
									v-if="kpis[types[wrap_item.targetable_type]][wrap_item.targetable_id]"
									class="fa fa-minus mt-1"
								/>
								<i
									v-else-if="loading[types[wrap_item.targetable_type]][wrap_item.targetable_id]"
									class="fa fa-circle-notch fa-spin mt-1"
								/>
								<i
									v-else
									class="fa fa-plus mt-1"
								/>
							</div>
						</td>
						<td class="p-4">
							<i
								v-if="types[wrap_item.targetable_type] === 1"
								class="fa fa-user mt-1 mr-1"
							/>
							<i
								v-else-if="types[wrap_item.targetable_type] === 2"
								class="fa fa-users mt-1 mr-1"
							/>
							<i
								v-else
								class="fa fa-briefcase mt-1 mr-1"
							/>
							<span v-if="wrap_item.target != null">{{ wrap_item.target.name }}</span>
							<span v-else>---</span>
						</td>
						<td
							v-if="editable"
							class="p-4"
							:style="`background-color: ${getBacklightForValue(wrap_item.avg)}`"
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
							:class="{'active': kpis[types[wrap_item.targetable_type]][wrap_item.targetable_id] || !editable }"
						>
							<td :colspan="editable ? 4 : 7">
								<div class="table__wrapper w-100">
									<table
										v-if="kpis[types[wrap_item.targetable_type]][wrap_item.targetable_id]"
										class="child-table"
									>
										<template v-for="(user, i) in kpis[types[wrap_item.targetable_type]][wrap_item.targetable_id].users">
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
												<td class="p-4">
													Средний % <b>{{ parseFloat(user.avg_percent).toFixed(2) }}%</b>
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
											<tr
												v-if="user.expanded"
												:key="i + 'a'"
											>
												<td :colspan="fields.length + 2">
													<div class="table__wrapper__second w-100">
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
															:editable="editable"
															:kpi_page="false"
															:date="date"
															:allow_overfulfillment="wrap_item.off_limit"
															@getSum="wrap_item.my_sum = $event"
															@recalced="countAvg"
														/>
													</div>
												</td>
											</tr>
										</template>
									</table>
								</div>
							</td>
						</tr>
					</template>
				</template>
			</tbody>
		</table>
	</div>
</template>

<script>
/* eslint-disable camelcase, vue/require-prop-types */

import { mapActions } from 'pinia'
import { usePortalStore } from '@/stores/Portal'
import KpiItemsV2 from '@/pages/kpi/KpiItemsV2'
import {
	kpi_fields,
	parseKPI,
} from './kpis.js'

export default {
	name: 'StatsTableV2',
	components: {
		KpiItemsV2,
	},
	props: {
		searchText: {
			default: '',
		},
		items: {
			default: [],
		},
		activities: {
			default: [],
		},
		groups: {
			default: [],
		},
		editable: {
			default: false
		},
		date: {
			default: null
		},
		filters: {
			type: Object,
			default: () => ({})
		}
	},

	data() {
		return {
			show_fields: [],
			all_fields: kpi_fields,
			fields: [],
			non_editable_fields: [
				'created_at',
				'updated_at',
				'created_by',
				'updated_by',
			],
			types: {
				'App\\User': 1,
				'App\\ProfileGroup': 2,
				'App\\Position': 3,
			},
			kpis: {
				1: {},
				2: {},
				3: {},
			},
			loading: {
				1: {},
				2: {},
				3: {},
			}
		}
	},

	computed:{
		reversedItems(){
			return this.items.slice().reverse()
		}
	},

	watch: {
		show_fields: {
			handler: function (val) {
				localStorage.kpi_show_fields = JSON.stringify(val);
				this.prepareFields();
			},
			deep: true
		},
		items(){
			this.resetKPI()
		}
	},

	created() {
		this.prepareFields()
		this.countAvg()
	},
	mounted(){
	},
	methods: {
		...mapActions(usePortalStore, ['getBacklightForValue']),
		async fetchKPI(id, ttype){
			const type = this.types[ttype]
			this.$set(this.loading[type], id, true)
			try{
				const { data } = await this.axios.post(`/statistics/kpi/groups-and-users/${id}`, {
					filters: {
						...this.filters,
						query: this.searchText,
					}
				}, {
					params: {
						type
					}
				})
				if(!data?.kpi?.users) return this.$toast.error('Ошибка при получении данных kpi')
				this.$set(this.kpis[type], id, parseKPI(data?.kpi))
			}
			catch(error){
				this.$toast.error('Ошибка при получении данных kpi')
			}
			this.$delete(this.loading[type], id)
		},
		closeKPI(id, ttype){
			const type = this.types[ttype]
			this.$delete(this.kpis[type], id)
		},
		resetKPI(){
			this.kpis = {
				1: {},
				2: {},
				3: {},
			}
		},

		prepareFields() {
			let visible_fields = []

			kpi_fields.forEach(field => {
				if(this.show_fields[field.key] != undefined
					&& this.show_fields[field.key]
				) {
					visible_fields.push(field)
				}
			});

			this.fields = kpi_fields;
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
