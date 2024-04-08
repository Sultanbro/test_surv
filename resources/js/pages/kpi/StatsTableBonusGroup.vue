<template>
	<div
		v-if="group"
		class="StatsTableBonusGroup"
	>
		<table
			v-for="(user, userIndex) in group.users"
			:key="userIndex"
			class="table b-table table-bordered table-sm table-responsive mb-0 table-inner"
		>
			<tr>
				<th
					class="b-table-sticky-column text-center px-1 pointer"
					@click="user.expanded = !user.expanded"
				>
					<div class="d-flex px-2 ">
						<i
							v-if="user.expanded"
							class="fa fa-minus mt-1"
						/>
						<i
							v-else
							class="fa fa-plus mt-1"
						/>
						<span class="ml-2 bg-transparent ">{{ user.id }}</span>
					</div>
				</th>
				<th
					class="text-left"
				>
					{{ user.full_name }}
				</th>
				<th
					v-for="bonus in group.bonuses"
					:key="bonus.id"
				>
					{{ bonus.title }}
					<b v-if="user.totals[bonus.id]">
						{{ user.totals[bonus.id] }} тг
					</b>
					<b v-else> 0 тг</b>
				</th>
			</tr>
			<tr v-if="user.expanded">
				<td
					:colspan="2 + group.bonuses.length"
				>
					<table class="table b-table table-bordered table-sm table-responsive mb-0 table-inner">
						<tr>
							<th />
							<th>Наименование активности</th>
							<th>Кол-во</th>
							<th>Вознаграждение</th>
							<th>Период</th>
							<th>Заработано</th>
						</tr>
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
							<td>{{ bonus.date }}</td>
							<td>{{ bonus.amount * bonus.quantity }}</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>
</template>

<script>
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
	methods: {},
}
</script>

<style lang="scss">
//.StatsTableBonusGroup{}
</style>
