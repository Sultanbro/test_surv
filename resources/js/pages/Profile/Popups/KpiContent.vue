<template>
	<div class="kpi__content">
		<ProfileTabs :tabs="items.slice().reverse().map(kpi => (kpi.target && kpi.target.name) || '---')">
			<template
				v-for="(wrap_item, w) in items.slice().reverse()"
				#[`tab(${w})`]
			>
				<div
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
									:key="w + 'a'"
									class="collapsable"
									:class="{'active': wrap_item.expanded || !editable }"
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
															class="pointer px-2"
															@click="user.expanded = !user.expanded"
														>
															<span class="ml-2 bg-transparent">{{ i + 1 }}</span>
														</td>
														<td class="px-2 py-1">
															{{ user.name }}
														</td>

														<template v-if="user.items !== undefined">
															<td
																v-for="kpi_item in user.items"
																:key="kpi_item"
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
															:class="{'active': true}"
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
			</template>
		</ProfileTabs>
	</div>
</template>

<script>
import KpiItemsV2 from '@/pages/kpi/KpiItemsV2.vue'
import ProfileTabs from '@ui/ProfileTabs'

export default {
	name: 'KpiContent',
	components: {
		KpiItemsV2,
		ProfileTabs,
	},
	props: {
		items: {
			type: Array,
			default: () => []
		},
		groups: {
			type: Array,
			default: () => []
		},
		activities: {
			type: Array,
			default: () => []
		},
		fields: {
			type: Array,
			default: () => []
		},
		editable: {
			type: Boolean,
			default: false
		}
	},
	methods: {
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

<style>
</style>
