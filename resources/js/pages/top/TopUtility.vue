<template>
	<div class="TopUtility d-flex flex-column">
		<TopGauges
			:key="ukey"
			:utility_items="activeUtility"
			:editable="true"
			wrapper_class=" br-1"
			page="top"
		/>

		<JobtronButton
			small
			secondary
			class="TopUtility-settings"
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
				:items="utilitySwitch"
				@change="onChangeSwitch"
			/>
		</SideBar>
	</div>
</template>

<script>
const TopGauges = () => import(/* webpackChunkName: "TopGauges" */ '@/components/TopGauges')  // TOП спидометры, есть и в аналитике
import JobtronButton from '@ui/Button'
import SideBar from '@ui/Sidebar'
import TopSwitches from '@/components/pages/Top/TopSwitches'

import { bus } from '@/bus'
import { mapState } from 'pinia'
import { useTopStore } from '@/stores/Top.js'
import {
	fetchTop,
	fetchArchiveUtility,
	// fetchArchiveRentability,
	// fetchArchiveProceeds,
	switchArchiveTop,
} from '@/stores/api'

export default {
	name: 'TopUtility',
	components: {
		TopGauges,
		JobtronButton,
		SideBar,
		TopSwitches,
	},
	props: {},
	data(){
		return {
			ukey: 0,
			utility: [],
			utilitySwitch: {},
			isArchiveOpen: false,
		}
	},
	computed: {
		...mapState(useTopStore, ['year', 'month']),
		activeUtility(){
			return this.utility.filter(util => this.utilitySwitch[util.id] && this.utilitySwitch[util.id].value)
		},
	},
	watch: {},
	created(){},
	mounted(){
		this.fetchData()
		this.fetchSwitches()
		bus.$on('tt-top-update', this.fetchData)
	},
	beforeDestroy(){
		bus.$off('tt-top-update', this.fetchData)
	},
	methods: {
		async fetchData(){
			const loader = this.$loading.show()

			try {
				const {
					utility,
				} = await fetchTop({
					month: this.month + 1,
					year: this.year,
				})

				this.utility = utility;

				this.ukey++;
			}
			catch (error) {
				alert(error)
			}

			loader.hide()
		},
		async fetchSwitches(){
			try {
				const { data: utility } = await fetchArchiveUtility()
				this.utilitySwitch = utility.reduce((result, group) => {
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
				[colField]: 'switch_utility',
				[valField]: value ? 1 : 0
			})
			const item = this.utilitySwitch[id]
			if(item){
				item.value = !item.value
				item.switch = item.value ? 1 : 0
			}
		},
	},
}
</script>

<style lang="scss">
.TopUtility{
	position: relative;
	&-settings{
		position: absolute;
		z-index: 1;
		right: 0;
		bottom: calc(100% + 8px);
	}
}
</style>
