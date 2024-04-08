<template>
	<div
		v-if="user"
		class="StatsTableBonusUser"
	>
		<table class="table b-table table-bordered table-sm table-responsive mb-0 table-inner">
			<tr>
				<th />
				<th>Наименование активности</th>
				<th>Кол-во</th>
				<th>Вознаграждение</th>
				<th>Дата</th>
				<th>Заработано</th>
			</tr>
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
					<td>{{ bonus.date }}</td>
					<td>{{ bonus.amount * bonus.quantity }}</td>
				</tr>
			</template>
		</table>
	</div>
</template>

<script>
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
	computed: {},
	watch: {},
	created(){},
	mounted(){},
	beforeDestroy(){},
	methods: {},
}
</script>

<style lang="scss">
//.StatsTableBonusUser{}
</style>
