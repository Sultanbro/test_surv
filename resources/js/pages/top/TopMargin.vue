<template>
	<div class="TopMargin">
		<TableRentability
			:year="+year"
			:month="(+month) + 1"
			:rentability-switch="rentabilitySwitch"
		/>

		<JobtronButton
			small
			secondary
			class="TopMargin-settings"
			@click="isArchiveOpen = true"
		>
			<i class="icon-nd-settings" />
		</JobtronButton>

		<SideBar
			title="Активные спидометры"
			width="35%"
			:open="isArchiveOpen"
			@close="isArchiveOpen = false"
		>
			<TopSwitches
				:items="rentabilitySwitch"
				@change="onChangeSwitch"
			/>
		</SideBar>
	</div>
</template>

<script>
import TableRentability from '@/components/tables/TableRentability' // ТОП рентабельность
import JobtronButton from '@ui/Button'
import SideBar from '@ui/Sidebar'
import TopSwitches from '@/components/pages/Top/TopSwitches'

import { mapState } from 'pinia'
import { useTopStore } from '@/stores/Top.js'
import {
	fetchArchiveRentability,
	switchArchiveTop,
} from '@/stores/api'

export default {
	name: 'TopMargin',
	components: {
		TableRentability,
		JobtronButton,
		SideBar,
		TopSwitches,
	},
	props: {},
	data(){
		return {
			isArchiveOpen: false,
			rentabilitySwitch: {},
		}
	},
	computed: {
		...mapState(useTopStore, ['year', 'month']),
	},
	watch: {},
	created(){
		this.fetchSwitches()
	},
	mounted(){},
	methods: {
		async fetchSwitches(){
			try {
				const { data: rentability } = await fetchArchiveRentability()
				this.rentabilitySwitch = rentability.reduce((result, group) => {
					result[group.id] = {
						...group,
						value: !!group.switch
					}
					return result
				}, {})
			}
			catch (error) {
				console.error('[fetchSwitches]', error)
			}
		},
		onChangeSwitch({id, value}){
			const colField = 'switch_column'
			const valField = 'switch_value'
			switchArchiveTop({
				id,
				[colField]: 'switch_rentability',
				[valField]: value ? 1 : 0
			})
			const item = this.rentabilitySwitch[id]
			if(item){
				item.value = !item.value
				item.switch = item.value ? 1 : 0
			}
		},
	},
}
</script>

<style lang="scss">
.TopMargin{
	position: relative;
	&-settings{
		position: absolute;
		z-index: 1;
		right: 0;
		bottom: calc(100% + 8px);
	}
}
</style>
