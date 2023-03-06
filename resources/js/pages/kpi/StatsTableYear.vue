<template>
	<div class="StatsTableYear">
		<table class="j-table">
			<thead>
				<tr class="table-heading">
					<th class="first-column">
						Сотрудник
					</th>
					<th class="text-center">
						Итог
					</th>
					<th class="text-center">
						N
					</th>
					<th
						v-for="month in $moment.months()"
						:key="month"
						class="text-center"
					>
						{{ month }}
					</th>
				</tr>
			</thead>
			<tbody>
				<tr
					v-for="user in stats"
					:key="user.id"
				>
					<td class="p-3">
						{{ user.name }}
					</td>
					<td class="text-center p-3">
						{{ user.total }}
					</td>
					<td class="text-center p-3">
						{{ user.n }}
					</td>
					<td
						v-for="month, key in $moment.months()"
						:key="key"
						class="text-center p-3"
					>
						{{ user[key] || '' }}
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</template>

<script>
export default {
	name: 'StatsTableYear',
	components: {},
	data(){
		return {
			stats: [
				{
					id: 0,
					name: 'test',
					total: 0,
					n: 0,
					0: 0,
					1: 0
				}
			]
		}
	}
}
</script>

<style>
</style>
