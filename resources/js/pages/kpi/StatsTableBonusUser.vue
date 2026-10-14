<template>
	<div
		v-if="user && countBonuses"
		class="StatsTableBonusUser"
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
				<template v-for="kpiBonus in user.bonuses">
					<tr
						v-for="(bonus, bonusIndex) in kpiBonus.obtained_bonuses"
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
				</template>
			</tbody>
		</table>
	</div>
</template>

<script>
import { ymd2dmy } from '@/lib/date.js'

export default {
	name: 'StatsTableBonusUser',
	components: {},
	props: {
		user: {
			type: Object,
			default: null
		},
	},
	data(){
		return {}
	},
	computed: {
		countBonuses(){
			if(!this.user) return 0
			return this.user.bonuses.map(kpiBonus => kpiBonus?.obtained_bonuses.length || 0).reduce((res, count) => res + count, 0)
		}
	},
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
//.StatsTableBonusUser{}
</style>
