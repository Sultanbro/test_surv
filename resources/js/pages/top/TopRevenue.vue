<template>
	<div class="TopRevenue">
		<div class="table-responsive table-container mt-4">
			<table class="table table-bordered whitespace-no-wrap custom-table-revenue">
				<thead>
					<tr>
						<th
							v-for="(field, findex) in proceeds.fields"
							:key="findex"
							class="t-name table-title"
							:class="{
								'w-295 b-table-sticky-column': findex == 0,
								'w-125': findex == 1,
								'w-80': findex == 2,
								'w-60': findex == 3,
								'text-center': findex != 0,
								'text-left': findex == 0,
							}"
						>
							<template v-if="['+/-'].includes(field)">
								<i
									v-b-popover.hover.right.html="'100% - ( План * Кол-во календарных дней )/ (Итого * Кол-во отработанных дней)'"
									class="fa fa-info-circle"
									title="Опережение плана"
								/>
							</template>
							<template v-if="['%'].includes(field)">
								<i
									v-b-popover.hover.right.html="'( Итого / План ) * 100'"
									class="fa fa-info-circle"
									title="Выполнение плана"
								/>
							</template>
							{{ field }}  <i
								v-if="field == 'Отдел'"
								class="fa fa-plus-square"
								@click="addRow()"
							/>
						</th>
					</tr>
				</thead>
				<tbody>
					<template v-for="(record, rindex) in proceeds.records">
						<tr
							v-if="(proceedsSwitch[record.group_id] && proceedsSwitch[record.group_id].value) || !record.group_id"
							:key="rindex"
						>
							<td
								v-for="(field, findex) in proceeds.fields"
								:key="findex"
								class="t-name table-title"
								:class="{
									'bg-grey': ['w1', 'w2', 'w3', 'w4', 'w5', 'w6'].includes(field),
									'weekend': isWeekend(field),
									'text-left b-table-sticky-column': ['Отдел'].includes(field)
								}"
							>
								<template v-if="!['%', 'План', 'Итого', '+/-', 'Отдел'].includes(field)">
									<div v-if="record['group_id'] < 0">
										<input
											v-model="record[field]"
											type="number"
											class="input"
											@change="updateProceed(record, field, 'day')"
										>
									</div>
									<div v-else>
										<span v-if="record[field] != 0">{{ record[field] }}</span>
										<span v-else />
									</div>
								</template>
								<template v-else>
									<template v-if="field == 'Отдел'">
										<a
											v-if="record['group_id'] >= 0"
											:href="'/timetracking/an?group='+ record['group_id'] + '&active=1&load=1'"
											target="_blank"
										>
											{{ record[field] }}
										</a>
										<div v-else>
											<input
												v-model="record[field]"
												type="text"
												class="input-2"
												@change="updateProceed(record, field, 'name')"
											>
										</div>
										<i
											v-if="record.deleted_at"
											v-b-popover.hover.right.html="'Аналитика архвирована ' + $moment(record.deleted_at, 'YYYY-MM-DD').format('DD.MM.YYYY')"
											class="fa fa-info-circle"
										/>
									</template>
									<template v-else>
										<div>
											{{ record[field] }}
										</div>
									</template>
								</template>
							</td>
						</tr>
					</template>
				</tbody>
			</table>
		</div>

		<JobtronButton
			small
			secondary
			class="TopRevenue-settings"
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
				:items="proceedsSwitch"
				@change="onChangeSwitch"
			/>
		</SideBar>
	</div>
</template>

<script>
import JobtronButton from '@ui/Button'
import SideBar from '@ui/Sidebar'
import TopSwitches from '@/components/pages/Top/TopSwitches'

import { bus } from '@/bus'
import { mapState } from 'pinia'
import { useTopStore } from '@/stores/Top.js'
import {
	fetchTop,
	// fetchArchiveUtility,
	// fetchArchiveRentability,
	fetchArchiveProceeds,
	switchArchiveTop,
} from '@/stores/api'

export default {
	name: 'TopRevenue',
	components: {
		JobtronButton,
		SideBar,
		TopSwitches,
	},
	props: {},
	data(){
		return {
			proceeds: [],
			proceedsSwitch: {},
			isArchiveOpen: false,
		}
	},
	computed: {
		...mapState(useTopStore, ['year', 'month']),
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
		async fetchData() {
			const loader = this.$loading.show()

			try {
				const {
					proceeds,
				} = await fetchTop({
					month: this.month + 1,
					year: this.year,
				})
				this.proceeds = proceeds
			}
			catch (error) {
				alert(error)
			}

			loader.hide()
		},
		async fetchSwitches(){
			try {
				const { data: proceeds } = await fetchArchiveProceeds()
				this.proceedsSwitch = proceeds.reduce((result, group) => {
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
				[colField]: 'switch_proceeds',
				[valField]: value ? 1 : 0
			})
			const item = this.proceedsSwitch[id]
			if(item){
				item.value = !item.value
				item.switch = item.value ? 1 : 0
			}
		},
		isWeekend(field) {
			var arr = field.split('.');
			var month = Number(arr[1]) - 1;
			var dayOfWeek = new Date(this.year, month, arr[0]).getDay();

			return dayOfWeek == 6 || dayOfWeek == 0;
		},
		addRow() {
			let length = this.proceeds.records.length
			let obj = {}
			this.proceeds.fields.forEach(field => {
				obj[field] = null
			})

			obj['group_id'] = this.proceeds.lowest_id - 1

			this.proceeds.records.splice(length - 1, 0, obj)
		},
		updateProceed(record, field, type) {
			let loader = this.$loading.show();

			const groupFiled = 'group_id'

			this.axios.post('/timetracking/top/proceeds/update', {
				[groupFiled]: record['group_id'],
				value: record[field],
				date: field == 'Отдел' ?  this.proceeds.fields[5] : field,
				name: record['Отдел'],
				type: type,
				year: this.year,
			}).then(() => {
				this.$toast.success('Успешно сохранено!');
				loader.hide()
			}).catch(error => {
				alert(error)
				loader.hide()
			});
		},
	},
}
</script>

<style lang="scss">
.TopRevenue{
	position: relative;
	&-settings{
		position: absolute;
		z-index: 1;
		right: 0;
		bottom: calc(100% + 24px);
	}
}
</style>
