<template>
	<div class="TableTraineeSage2">
		<JobtronTable
			:items="ocenkaSvod"
			:fields="fields"
		>
			<template #header(sent)>
				Кол-во <br>переданных <br> стажеров
			</template>
			<template #header(working)>
				Кол-во <br>приступивших <br>к работе
			</template>
			<template #header(percent)>
				Процент <br>прохождения<br> стажировки
			</template>
			<template #header(active)>
				Кол-во<br> стажирующихся активных
				<i
					class="fa fa-info-circle"
					v-b-popover.hover.right.html="'Стажеры, которые присутстовали на сегодня. В табели у них есть оранжевая отметка.'"
					title="Активные стажеры"
				/>
			</template>
		</JobtronTable>
	</div>
</template>

<script>
import JobtronTable from '@ui/Table'

export default {
	name: 'TableTraineeSage2',
	components: {
		JobtronTable,
	},
	props: {
		ocenkaSvod: {
			type: Array,
			default: () => []
		}
	},
	data(){
		const headerClass = 'text-left TableTraineeSage2-header'
		const cellClass = 'text-center'
		return {
			fields: [
				{
					key: 'name',
					label: 'Отдел',
					thClass: headerClass,
					tdClass: headerClass
				},
				{
					key: 'required',
					label: 'Требуется нанять',
					thClass: cellClass,
					tdClass: cellClass,
				},
				{
					key: 'sent',
					label: '',
					thClass: cellClass,
					tdClass: cellClass,
				},
				{
					key: 'working',
					label: '',
					thClass: cellClass,
					tdClass: cellClass,
				},
				{
					key: 'percent',
					label: '',
					thClass: cellClass,
					tdClass: cellClass,
				},
				{
					key: 'active',
					label: '',
					thClass: cellClass,
					tdClass: cellClass,
				},
			]
		}
	}
}
</script>

<style lang="scss">
.TableTraineeSage2{
	&-header{
		background:#90d3ff;
		width:250px;
	}
}
</style>
