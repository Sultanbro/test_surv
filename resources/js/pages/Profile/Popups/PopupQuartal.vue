<template>
	<div
		class="popup__content mt-3"
		:class="{'v-loading': loading}"
	>
		<div class="popup__filter pb-4">
			<DateSelect
				v-model="selectedDate"
				class="ml-a Balance-datePicker"
			/>
		</div>
		<div class="popup__award">
			<b-tabs
				v-if="itemsUser.length || itemsGroup.length || itemsPosition.length"
				v-model="tabIndex"
				class="overflow-hidden"
			>
				<b-tab
					v-if="itemsUser.length"
					title="Индивидуальные"
				>
					<template v-for="(item, idxUser) in itemsUser">
						<PopupQuartalItem
							:key="idxUser"
							:item="item"
						/>
					</template>
				</b-tab>
				<b-tab
					v-if="itemsGroup.length"
					title="По отделу"
				>
					<template v-for="(item, idxGroup) in itemsGroup">
						<PopupQuartalItem
							:key="idxGroup"
							:item="item"
						/>
					</template>
				</b-tab>
				<b-tab
					v-if="itemsPosition.length"
					title="По должности"
				>
					<template v-for="(item, idxPosition) in itemsPosition">
						<PopupQuartalItem
							:key="idxPosition"
							:item="item"
						/>
					</template>
				</b-tab>
			</b-tabs>
			<p
				v-else
				class="font-16 text-muted mt-3"
			>
				Обратитесь к своему руководителю, если хотите чтобы вам была назначена квартальная премия
			</p>
		</div>
	</div>
</template>

<script>
import { mapState, mapActions } from 'pinia'
import { usePortalStore } from '@/stores/Portal'
import { useProfileSalaryStore } from '@/stores/ProfileSalary'
import { useYearOptions } from '@/composables/yearOptions'

import DateSelect from '../DateSelect'
import PopupQuartalItem from './PopupQuartalItem'

export default {
	name: 'PopupQuartal',
	components: {
		DateSelect,
		PopupQuartalItem,
	},
	props: {},
	data: function () {
		return {
			itemsUser: [],
			itemsGroup: [],
			itemsPosition: [],
			activities: [],
			groups: [],
			dateInfo: {
				currentMonth: null,
				monthEnd: 0,
				workDays: 0,
				weekDays: 0,
				daysInMonth: 0
			},
			loading: false,
			selectedDate: this.$moment().format('DD.MM.YYYY'),
			tabIndex: 0,
		};
	},
	computed: {
		...mapState(usePortalStore, ['portal']),
		currentMonth(){
			return this.$moment(this.selectedDate, 'DD.MM.YYYY').format('MMMM')
		},
		currentYear(){
			return this.$moment(this.selectedDate, 'DD.MM.YYYY').format('YYYY')
		},
		years(){
			if(!this.portal.created_at) return [new Date().getFullYear()]
			return useYearOptions(new Date(this.portal.created_at).getFullYear())
		},
		showedTabs(){
			return [
				this.itemsUser,
				this.itemsGroup,
				this.itemsPosition,
			].filter(tab => tab.length)
		}
	},
	watch: {
		selectedDate(){
			this.$nextTick(() => {
				this.fetchBefore()
			})
		},
		tabIndex(){
			this.setSubtitle()
		}
	},
	created(){
		this.setMonth()
		this.fetchBefore()
	},
	methods: {
		...mapActions(useProfileSalaryStore, ['setReadedPremiums']),

		setMonth() {
			let year = this.$moment().format('YYYY')
			this.dateInfo.currentMonth = this.dateInfo.currentMonth ? this.dateInfo.currentMonth : this.$moment().format('MMMM')
			// this.currentMonth = this.dateInfo.currentMonth;
			this.dateInfo.date = `${this.dateInfo.currentMonth} ${year}`

			let currentMonth = this.$moment(this.dateInfo.currentMonth, 'MMMM')

			// Расчет выходных дней
			this.dateInfo.monthEnd = currentMonth.endOf('month'); //Конец месяца
			this.dateInfo.weekDays = currentMonth.weekdayCalc(this.dateInfo.monthEnd, [6]) //Колличество выходных
			this.dateInfo.daysInMonth = currentMonth.daysInMonth() //Колличество дней в месяце
			this.dateInfo.workDays = this.dateInfo.daysInMonth - this.dateInfo.weekDays //Колличество рабочих дней
		},

		fetchBefore() {
			/* eslint-disable camelcase */
			this.fetchData({
				data_from: {
					year: this.currentYear,
					month: this.$moment(this.currentMonth, 'MMMM').format('M')
				},
				user_id: this.$laravel.userId
			})
			/* eslint-enable camelcase */
		},

		async fetchData(filters = null) {
			this.loading = true

			try {
				const { data } = await this.axios.post('/statistics/quartal-premiums', {
					filters: filters
				})

				if(data?.data){
					this.itemsUser = data.data[0];
					this.itemsGroup = data.data[1];
					this.itemsPosition = data.data[2];
				}

				this.setReadedPremiums()
				this.setSubtitle()
			}
			catch (error) {
				window.onerror && window.onerror(error)
				alert(error)
			}

			this.loading = false
		},

		setSubtitle(){
			const group = this.showedTabs[this.tabIndex]
			if(group) {
				const item = [0]
				this.$emit('title', `За период с ${ new Date(item.from).toLocaleDateString('RU') } до ${ new Date(item.to).toLocaleDateString('RU') }`)
			}
		},

		defineSourcesAndGroups() {
			/* eslint-disable camelcase */
			this.items.forEach(p => {
				p.items.forEach(el => {
					el.source = 0;
					el.group_id = 0;

					if(el.activity_id != 0) {
						let i = this.activities.findIndex(a => a.id == el.activity_id);
						if(i != -1) {
							el.source = this.activities[i].source
							if(el.source == 1) el.group_id = this.activities[i].group_id
						}
					}
				});
			})
			/* eslint-enable camelcase */
		},
	}
};
</script>

<style lang="scss">
	.popup__award{
		.nav-tabs {
			border-top: 1px solid #dee2e6;
			flex-wrap: nowrap;
			white-space: nowrap;
			.nav-item {
				.nav-link {
					font-size: 2.1rem;
					border-bottom: none;
					margin-top: 0.1rem;
					line-height: 2em;
					color: #8D8D8D;
					font-family: "Open Sans", sans-serif;
					font-weight: 600;
					transition: color 0.3s;
					padding: 1.5rem 0 0 0;
					cursor: pointer;
					margin-right: 40px;
					background-color: transparent;
					border-top: 4px solid transparent;

					&:hover {
						border-color: transparent;
						color: #ED2353;
					}

					&.active {
						border-top: 4px solid #ED2353;
						color: #ED2353;
					}
				}
			}
		}
	}
</style>
