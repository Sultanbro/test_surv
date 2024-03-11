<template>
	<div class="bonuses">
		<table class="table j-table table-bordered table-sm mb-3 collapse-table">
			<tr>
				<th class="b-table-sticky-column text-center px-1">
					<i class="fa fa-cog" />
				</th>
				<th class="text-left">
					Квартальные премии
				</th>
			</tr>
			<tr />
			<template v-for="(page_item, p) in users">
				<template v-if="page_item.name.includes(searchText) || searchText.length == 0">
					<tr :key="p">
						<td
							class="pointer b-table-sticky-column"
							@click="expandUser(p)"
						>
							<div class="d-flex px-2">
								<i
									v-if="page_item.expanded"
									class="fa fa-minus mt-1"
								/>
								<i
									v-else
									class="fa fa-plus mt-1"
								/>
								<span class="ml-2">{{ p + 1 }}</span>
							</div>
						</td>
						<td class="text-left">
							<div class="d-flex aic p-1">
								<i class="fa fa-user ml-2 color-user" />
								<span class="ml-2">{{ page_item.name }}</span>
							</div>
						</td>
					</tr>
					<template v-if="page_item.expanded">
						<tr :key="'tr' + p">
							<td colspan="2">
								<table class="table b-table table-bordered table-sm table-responsive mb-0 table-inner">
									<tr>
										<th />
										<th>Название</th>
										<th>Вид плана</th>
										<th>План</th>
										<th>Факт</th>
										<th>Вознаграждение</th>
										<th>Период с</th>
										<th>Период по</th>
										<th>Текст</th>
										<th>Вып/Невып</th>
										<th>Заработанная сумма</th>
									</tr>
									<tr>
										<td />
										<td>{{ page_item.items.title }}</td>
										<td>{{ methods[page_item.items.method] || '' }}</td>
										<td>{{ page_item.items.plan }}</td>
										<td>{{ page_item.items.fact }}</td>
										<td>{{ page_item.items.sum }}</td>
										<td>{{ page_item.items.from }}</td>
										<td>{{ page_item.items.to }}</td>
										<td>{{ page_item.items.text }}</td>
										<td>
											{{ isCompleted(page_item.items.plan, page_item.items.fact, page_item.items.method) ? 'Выполнено' : 'Не выполнено' }}
										</td>
										<td>
											{{ isCompleted(page_item.items.plan, page_item.items.fact, page_item.items.method) ? page_item.items.sum : 0 }}
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</template>
				</template>
			</template>
			<template v-for="(page_item, p) in groups">
				<template v-if="page_item[0].name.includes(searchText) || searchText.length == 0">
					<tr :key="p">
						<td
							class="pointer b-table-sticky-column"
							@click="expandGroup(p)"
						>
							<div class="d-flex px-2">
								<i
									v-if="page_item.expanded"
									class="fa fa-minus mt-1"
								/>
								<i
									v-else
									class="fa fa-plus mt-1"
								/>
								<span class="ml-2">{{ p + 1 + users.length }}</span>
							</div>
						</td>
						<td class="text-left">
							<div class="d-flex aic p-1">
								<i class="fa fa-users ml-2 color-group" />
								<span class="ml-2">{{ page_item[0].name }}</span>
							</div>
						</td>
					</tr>
					<template v-if="page_item.expanded">
						<tr :key="'tr' + p">
							<td colspan="2">
								<template v-for="(user, i) in page_item">
									<table
										v-if="i != 'expanded'"
										:key="i"
										class="table b-table table-bordered table-sm table-responsive mb-0 table-inner"
									>
										<tr>
											<th
												class="pointer b-table-sticky-column"
												@click="expandGroupUser(i, p)"
											>
												<div class="d-flex px-2">
													<i
														v-if="user.expended"
														class="fa fa-minus mt-1"
													/>
													<i
														v-else
														class="fa fa-plus mt-1"
													/>
													<span class="ml-2">{{ parseInt(i) + 1 }}</span>
												</div>
											</th>
											<th colspan="9">
												{{ user.full_name }}
											</th>
										</tr>
										<template v-if="user.expended">
											<tr>
												<th />
												<th>Название</th>
												<th>Вид плана</th>
												<th>План</th>
												<th>Факт</th>
												<th>Вознаграждение</th>
												<th>Период с</th>
												<th>Период по</th>
												<th>Текст</th>
												<th>Вып/Невып</th>
												<th>Заработанная сумма</th>
											</tr>
											<tr>
												<td />
												<td>{{ user.quartalPremiums.title }}</td>
												<td>{{ methods[user.quartalPremiums.method] || '' }}</td>
												<td>{{ user.quartalPremiums.plan }}</td>
												<td>{{ user.fact }}</td>
												<td>{{ user.quartalPremiums.sum }}</td>
												<td>{{ user.quartalPremiums.from }}</td>
												<td>{{ user.quartalPremiums.to }}</td>
												<td>{{ user.quartalPremiums.text }}</td>
												<td>
													{{ isCompleted(user.quartalPremiums.plan, user.quartalPremiums.fact, user.quartalPremiums.method) ? 'Выполнено' : 'Не выполнено' }}
												</td>
												<td>
													{{ isCompleted(user.quartalPremiums.plan, user.quartalPremiums.fact, user.quartalPremiums.method) ? page_item.quartalPremiums.sum : 0 }}
												</td>
											</tr>
										</template>
									</table>
								</template>
							</td>
						</tr>
					</template>
				</template>
			</template>
		</table>
	</div>
</template>

<script>
/* eslint-disable vue/no-mutating-props */
import { sumMethods } from './helpers.js';
export default {
	name: 'StatsTableQuartal',
	props: {
		users: {
			type: Array,
			default: () => []
		},
		groups: {
			type: Array,
			default: () => []
		},
		searchText: {
			type: String,
			default: ''
		}
	},
	data() {
		return {
			methods: sumMethods,
		}
	},
	computed: {
	},
	watch: {
	},

	created() {
	},

	mounted() {
	},
	methods: {
		expandUser(i){
			this.users[i].expanded = !this.users[i].expanded
		},
		expandGroup(i){
			this.groups[i].expanded = !this.groups[i].expanded
		},
		expandGroupUser(i,p){
			this.groups[p][i].expended = !this.groups[p][i].expended
		},
		isCompleted(plan, fact, method){
			plan = Number(plan)
			fact = Number(fact)
			method = Number(method)

			switch(method){
			case 1:
				return fact > plan
			case 3:
				return fact < plan
			case 5:
				return fact >= plan
			}
			return false
		}
	},

}
</script>
