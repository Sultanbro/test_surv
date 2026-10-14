<template>
	<div class="PopupQuartalItem">
		<div class="award__title popup__content-title">
			{{ item.text }}
		</div>
		<table class="table table-inner">
			<thead>
				<tr>
					<th />
					<th>Наименование активности</th>
					<th>Вид плана</th>
					<th>Целевое значение на месяц</th>
					<!-- <th>Удельный вес, %</th> -->
					<th>
						Факт
					</th>
					<th>
						% выполнения
					</th>
					<th>Сумма премии при выполнении плана, KZT</th>
					<th>Заработано</th>
				</tr>
			</thead>

			<tbody>
				<tr class="jt-row">
					<td class="text-center">
						{{ index + 1 }}
					</td>
					<td class="px-2">
						{{ item.title }}
					</td>
					<td class="text-center">
						{{ methods[item.method] }}
					</td>
					<td class="text-center">
						<b>{{ item.plan }} {{ item.unit || '' }}</b>
					</td>
					<!-- <td class="text-center">
						{{ item.share }}
					</td> -->
					<td class="text-center">
						{{ item.fact }}
					</td>
					<td class="text-center">
						{{ isCompleted(item.plan, item.fact, item.method) ? 'Выполнено' : 'Не выполнено' }}
					</td>
					<td class="text-center">
						{{ item.sum }}
					</td>
					<td class="text-center">
						{{ isCompleted(item.plan, item.fact, item.method) ? item.sum : 0 }}
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</template>

<script>
import { sumMethods } from '@/pages/kpi/helpers.js';

export default {
	name: 'PopupQuartalItem',
	components: {},
	props: {
		item: {
			type: Object,
			required: true,
		},
		index: {
			type: Number,
			default: 0
		}
	},
	data(){
		return {
			methods: sumMethods,
		}
	},
	computed: {},
	watch: {},
	created(){},
	mounted(){},
	beforeDestroy(){},
	methods: {
		isCompleted(plan, fact, method = 1){
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

<style lang="scss">
.PopupQuartalItem{
	th{
		font-size: 1.2rem;
		text-align: center;
	}
	td{
		vertical-align: middle;
	}
}
</style>
