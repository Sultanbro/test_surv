<template>
	<div class="TopPredicts">
		<div class="forecast table-container">
			<table class="table table-bordered table-custom-forecast">
				<thead>
					<th class="text-left t-name table-title td-blue">
						Отдел

						<i
							v-b-popover.hover.right.html="'Прогноз по принятию сотрудников на месяц'"
							class="fa fa-info-circle"
							title="Отдел"
						/>
					</th>
					<th class="text-center t-name table-title">
						План

						<i
							v-b-popover.hover.right.html="'Общий план операторов на проект от Заказчика'"
							class="fa fa-info-circle"
							title="План"
						/>
					</th>
					<th class="text-center t-name table-title">
						Факт

						<i
							v-b-popover.hover.right.html="'Фактически работают в группе на должности оператора'"
							class="fa fa-info-circle"
							title="Факт"
						/>
					</th>
					<th class="text-center t-name table-title">
						Осталось принять
					</th>
				</thead>
				<tbody>
					<tr
						v-for="(group, index) in predicts"
						:key="index"
					>
						<td class="text-left t-name table-title td-blue align-middle">
							{{ group.name }}
						</td>
						<td class="text-center t-name table-title align-middle">
							<input
								v-model="group.plan"
								type="number"
								@change="updatePredicts(index)"
							>
						</td>
						<td class="text-center t-name table-title align-middle">
							{{ group.users.employees }}
						</td>
						<td class="text-center t-name table-title align-middle">
							{{ group.plan - group.users.employees }}
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</template>

<script>
import {
	fetchTopPredicts,
	updateTopPredicts,
} from '@/stores/api/top.js'

export default {
	name: 'TopPredicts',
	components: {},
	props: {},
	data(){
		return {
			predicts: []
		}
	},
	computed: {},
	watch: {},
	created(){},
	mounted(){
		this.fetchPredicts()
	},
	methods: {
		async fetchPredicts(){
			const loader = this.$loading.show()
			try {
				const predicts = await fetchTopPredicts()
				this.predicts = predicts || []
			}
			catch (error) {
				console.error(error)
			}
			loader.hide()
		},
		async updatePredicts(index) {
			const loader = this.$loading.show()
			const group = this.predicts[index]
			try {
				await updateTopPredicts({
					groupId: group.id,
					plan: group.plan,
				})
				this.$toast.success('Успешно сохранено!')
			}
			catch (error) {
				console.error(error)
				this.$toast.success('Не удалось сохранить')
			}
			loader.hide()
		},
	},
}
</script>

<style lang="scss">
//.TopPredicts{}
</style>
