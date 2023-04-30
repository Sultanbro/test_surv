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
							@click="expand_user(p)"
							class="pointer b-table-sticky-column"
						>
							<div class="d-flex px-2">
								<i
									class="fa fa-minus mt-1"
									v-if="page_item.expanded"
								/>
								<i
									class="fa fa-plus mt-1"
									v-else
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
										<td>{{ page_item.items.plan }}</td>
										<td>{{ page_item.items.fact }}</td>
										<td>{{ page_item.items.sum }}</td>
										<td>{{ page_item.items.from }}</td>
										<td>{{ page_item.items.to }}</td>
										<td>{{ page_item.items.text }}</td>
										<td v-if="page_item.items.plan <= page_item.items.fact">
											Выполнено
										</td>
										<td v-else>
											Не выполнено
										</td>
										<td v-if="page_item.items.plan <= page_item.items.fact">
											{{ page_item.items.sum }}
										</td>
										<td v-else>
											0
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
							@click="expand_group(p)"
							class="pointer b-table-sticky-column"
						>
							<div class="d-flex px-2">
								<i
									class="fa fa-minus mt-1"
									v-if="page_item.expanded"
								/>
								<i
									class="fa fa-plus mt-1"
									v-else
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
												@click="expand_group_user(i, p)"
												class="pointer b-table-sticky-column"
											>
												<div class="d-flex px-2">
													<i
														class="fa fa-minus mt-1"
														v-if="user.expended"
													/>
													<i
														class="fa fa-plus mt-1"
														v-else
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
												<td>{{ user.quartalPremiums.plan }}</td>
												<td>{{ user.fact }}</td>
												<td>{{ user.quartalPremiums.sum }}</td>
												<td>{{ user.quartalPremiums.from }}</td>
												<td>{{ user.quartalPremiums.to }}</td>
												<td>{{ user.quartalPremiums.text }}</td>
												<td v-if="user.quartalPremiums.plan <= user.quartalPremiums.fact">
													Выполнено
												</td>
												<td v-else>
													Не выполнено
												</td>
												<td v-if="user.quartalPremiums.plan <= user.quartalPremiums.fact">
													{{ page_item.quartalPremiums.sum }}
												</td>
												<td v-else>
													0
												</td>
											</tr>
										</template>
										<!--<template v-if="user.expanded">
                                        <tr>
                                            <td></td>
                                            <td>{{user.quartalPremiums.title}}</td>
                                            <td>{{user.quartalPremiums.plan}}</td>
                                            <td>{{user.fact}}</td>
                                            <td>{{user.quartalPremiums.sum}}</td>
                                            <td>{{user.quartalPremiums.from}}</td>
                                            <td>{{user.quartalPremiums.to}}</td>
                                            <td>{{user.quartalPremiums.text}}</td>
                                            <td v-if="user.quartalPremiums.plan <= user.quartalPremiums.fact">Выполнено</td>
                                            <td v-else>Не выполнено</td>
                                            <td v-if="user.quartalPremiums.plan <= user.quartalPremiums.fact">{{page_item.quartalPremiums.sum}}</td>
                                            <td v-else>0</td>
                                        </tr>
                                    </template>-->
										<!--<tr>
                                        <th></th>
                                        <th>Название</th>
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
                                        <td></td>
                                        <td>{{page_item.quartalPremiums.title}}</td>
                                        <td>{{page_item.quartalPremiums.plan}}</td>
                                        <td>{{page_item.fact}}</td>
                                        <td>{{page_item.quartalPremiums.sum}}</td>
                                        <td>{{page_item.quartalPremiums.from}}</td>
                                        <td>{{page_item.quartalPremiums.to}}</td>
                                        <td>{{page_item.quartalPremiums.text}}</td>
                                        <td v-if="page_item.quartalPremiums.plan <= page_item.quartalPremiums.fact">Выполнено</td>
                                        <td v-else>Не выполнено</td>
                                        <td v-if="page_item.quartalPremiums.plan <= page_item.quartalPremiums.fact">{{page_item.quartalPremiums.sum}}</td>
                                        <td v-else>0</td>
                                    </tr>-->
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
export default {
	name: 'StatsTableQuartal',
	props: {
		users: Array,
		groups: Array,
		searchText: String
	},
	watch: {
	},
	data() {
		return {
		}
	},

	created() {
	},

	mounted() {
	},
	computed: {
	},
	methods: {
		expand_user(i){
			this.users[i].expanded = !this.users[i].expanded
		},
		expand_group(i){
			this.groups[i].expanded = !this.groups[i].expanded
		},
		expand_group_user(i,p){
			this.groups[p][i].expended = !this.groups[p][i].expended
		}
	},

}
</script>
