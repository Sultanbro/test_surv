<template>
	<div class="PopupQuartalItem">
		<div
			:key="'title-' + idxUser"
			class="award__title popup__content-title"
		>
			{{ item.text }}
		</div>
		<table
			:key="'table-' + idxUser"
			class="award__table"
		>
			<tr>
				<td class="blue">
					Сумма премии
				</td>
				<td>{{ item.items.sum }}</td>
			</tr>
			<tr v-if="item.items.activity">
				<td class="blue">
					План
				</td>
				<td>
					<div>
						<b>Активность: {{ item.items.activity.name }}</b>
					</div>
					<div>{{ item.items.plan }}</div>
				</td>
			</tr>
		</table>
	</div>
</template>

<script>
export default {
	name: 'PopupQuartalItem',
	components: {},
	props: {
		item: {
			type: Object,
			required: true,
		}
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
//.PopupQuartalItem{}
</style>
