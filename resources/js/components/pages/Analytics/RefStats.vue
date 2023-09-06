<template>
	<div class="RefStats">
		<JobtronTable
			:fields="tableFields"
			:items="users"
			:tr-after-class-fn="rowAfterClass"
		>
			<template #cell(switch)="{item}">
				<div
					v-if="item.users.length"
					class="RefStats-switch pointer"
					@click="toggleAfter(item.id)"
				>
					{{ uncollapsed.includes(item.id) ? '-' : '+' }}
				</div>
			</template>
			<template #afterRow="firstLayerData">
				<div
					v-if="firstLayerData.value.users.length"
					class="RefStats-subtable"
				>
					<JobtronTable
						:fields="[
							{key: 'switch', label: ''},
							...subTableFields
						]"
						:items="firstLayerData.value.users"
						:tr-after-class-fn="rowAfterClass"
					>
						<template #cell(switch)="{item}">
							<div
								v-if="item.users.length"
								class="RefStats-switch pointer"
								@click="toggleAfter(item.id)"
							>
								{{ uncollapsed.includes(item.id) ? '-' : '+' }}
							</div>
						</template>
						<template #afterRow="secondLayerData">
							<div
								v-if="secondLayerData.value.users.length"
								class="RefStats-subtable"
							>
								<JobtronTable
									:fields="[
										{key: 'switch', label: ''},
										...subTableFields
									]"
									:items="secondLayerData.value.users"
									:tr-after-class-fn="rowAfterClass"
								>
									<template #cell(switch)="{item}">
										<div
											v-if="item.users.length"
											class="RefStats-switch pointer"
											@click="toggleAfter(item.id)"
										>
											{{ uncollapsed.includes(item.id) ? '-' : '+' }}
										</div>
									</template>
									<template #afterRow="thirdLayerData">
										<div
											v-if="thirdLayerData.value.users.length"
											class="RefStats-subtable"
										>
											<JobtronTable
												:fields="subTableFields"
												:items="thirdLayerData.value.users"
												:tr-after-class-fn="rowAfterClass"
											/>
										</div>
									</template>
								</JobtronTable>
							</div>
						</template>
					</JobtronTable>
				</div>
			</template>
		</JobtronTable>
	</div>
</template>

<script>
import JobtronTable from '@ui/Table.vue'
import {
	tableFields,
	subTableFields,
	getFakeReferer,
} from './helper'

// const fakeData = {
// 	userPrice: 100,
// 	cvResultDealPercent: 85,
// 	cvDealUserPercent: 69,
// 	totalMonth: 1000,
// 	totalPayd: 800,
// }

export default {
	name: 'RefStats',
	components: {
		JobtronTable,
	},
	data(){
		return {
			users: [],
			tableFields,
			subTableFields,
			uncollapsed: [],
		}
	},
	mounted(){
		this.fetchData()
	},
	methods: {
		async fetchData(){
			this.users = [
				getFakeReferer(),
				getFakeReferer(),
			]
		},
		toggleAfter(id){
			const index = this.uncollapsed.findIndex(uId => uId === id)
			if(~index){
				this.uncollapsed.splice(index, 1)
			}
			else{
				this.uncollapsed.push(id)
			}
		},
		rowAfterClass(row){
			return this.uncollapsed.includes(row.id) ? 'RefStats-afterRowActive' : ''
		}
	},
}
</script>

<style lang="scss">
.RefStats{
	overflow-x: auto;
	.JobtronTable-afterRow{
		display: none;
		&.RefStats-afterRowActive{
			display: table-row;
		}
		.JobtronTable,
		.JobtronTable-head .JobtronTable-row:first-child .JobtronTable-th:first-child,
		.JobtronTable-head .JobtronTable-row:first-child .JobtronTable-th:last-child{
			border-radius: 0;
		}
		.JobtronTable-head .JobtronTable-row:first-child .JobtronTable-th:first-child::before{
			display: none;
		}
	}
	&-subtable{
		margin: -12px -15px;
	}
}
</style>
