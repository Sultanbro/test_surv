<template>
	<div
		v-if="group"
		class="StatsTableBonusGroup"
	>
		<table
			v-for="(user, userIndex) in group.users"
			:key="userIndex"
			class="StatsTableBonusGroup-table table b-table table-bordered table-sm mb-0 table-inner child-table"
		>
			<tr class="child-row">
				<td
					class="text-center p-2 pointer"
					@click="user.expanded = !user.expanded"
				>
					<div class="d-flex px-2 ">
						<template v-if="user.obtained_bonuses && user.obtained_bonuses.lengtd">
							<i
								v-if="user.expanded"
								class="fa fa-minus mt-1"
							/>
							<i
								v-else
								class="fa fa-plus mt-1"
							/>
						</template>
						<span class="ml-2 bg-transparent">
							{{ userIndex }}
						</span>
					</div>
				</td>
				<td
					class="text-left p2 StatsTableBonusGroup-name"
				>
					{{ user.last_name }} {{ user.name }}
				</td>
				<td
					v-for="bonus in group.bonuses"
					:key="bonus.id"
					class="p2"
				>
					{{ bonus.title }}
					<b v-if="user.totals[bonus.id]">
						{{ user.totals[bonus.id] }} тг
					</b>
					<b v-else> 0 тг</b>
				</td>
				<!-- <td /> -->
			</tr>
			<tr v-if="user.expanded && user.obtained_bonuses && user.obtained_bonuses.length">
				<td
					:colspan="2 + group.bonuses.length"
				>
					<table class="table b-table table-bordered table-sm table-responsive mb-0 table-inner">
						<thead>
							<tr>
								<th />
								<th>Наименование активности</th>
								<th>Кол-во</th>
								<th>Вознаграждение</th>
								<th>Дата</th>
								<th>Заработано</th>
							</tr>
						</thead>
						<tbody>
							<tr
								v-for="(bonus, bonusIndex) in user.obtained_bonuses"
								:key="bonusIndex"
							>
								<td class="text-white text-center">
									{{ bonusIndex + 1 }}
								</td>
								<td>{{ bonus.comment.split(':')[0] }}</td>
								<td>{{ bonus.quantity }}</td>
								<td>{{ bonus.amount }}</td>
								<td>{{ ymd2dmy(bonus.date) }}</td>
								<td>{{ bonus.amount * bonus.quantity }}</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		</table>
	</div>
</template>

<script>
import { ymd2dmy } from '@/lib/date.js'

export default {
	name: 'StatsTableBonusGroup',
	components: {},
	props: {
		group: {
			type: Object,
			default: null
		},
	},
	data(){
		return {}
	},
	computed: {},
	watch: {},
	created(){},
	mounted(){},
	beforeDestroy(){},
	methods: {
		ymd2dmy,
	},
}
</script>

<style lang="scss">
.StatsTableBonusGroup{
	&-table{}
	&-name{
		width: 225px;
		max-width: 225px;
		white-space: nowrap;
		text-overflow: ellipsis;
		overflow: hidden;
	}
	&-spacer{
		width: 100%;
	}
}
</style>
