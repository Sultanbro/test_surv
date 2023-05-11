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
				class="overflow-hidden"
				v-if="itemsUser.length || itemsGroup.length || itemsPosition.length"
			>
				<b-tab
					title="Индивидуальные"
					v-if="itemsUser.length"
				>
					<template v-for="(item, idxUser) in itemsUser">
						<div
							class="award__title popup__content-title"
							:key="'title-' + idxUser"
						>
							За период с {{ new Date(item.items.from).toLocaleDateString('RU') }} до {{ new Date(item.items.to).toLocaleDateString('RU') }}
						</div>
						<table
							class="award__table"
							:key="'table-' + idxUser"
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
							<tr>
								<td class="blue">
									Условия
								</td>
								<td>{{ item.items.text }}</td>
							</tr>
						</table>
					</template>
				</b-tab>
				<b-tab
					title="По отделу"
					v-if="itemsGroup.length"
				>
					<template v-for="(item, idxGroup) in itemsGroup">
						<div
							class="award__title popup__content-title"
							:key="'title-' + idxGroup"
						>
							За период с {{ new Date(item.from).toLocaleDateString('RU') }} до {{ new Date(item.to).toLocaleDateString('RU') }}
						</div>
						<table
							class="award__table"
							:key="'table-' + idxGroup"
						>
							<tr>
								<td class="blue">
									Сумма премии
								</td>
								<td>{{ item.sum }}</td>
							</tr>
							<tr v-if="item.activity">
								<td class="blue">
									План
								</td>
								<td>
									<div>
										<b>Активность: {{ item.activity.name }}</b>
									</div>
									<div>{{ item.plan }}</div>
								</td>
							</tr>
							<tr>
								<td class="blue">
									Условия
								</td>
								<td>{{ item.text }}</td>
							</tr>
						</table>
					</template>
				</b-tab>
				<b-tab
					title="По должности"
					v-if="itemsPosition.length"
				>
					<template v-for="(item, idxPosition) in itemsPosition">
						<div
							class="award__title popup__content-title"
							:key="'title-' + idxPosition"
						>
							За период с {{ new Date(item.from).toLocaleDateString('RU') }} до {{ new Date(item.to).toLocaleDateString('RU') }}
						</div>
						<table
							class="award__table"
							:key="'table-' + idxPosition"
						>
							<tr>
								<td class="blue">
									Сумма премии
								</td>
								<td>{{ item.sum }}</td>
							</tr>
							<tr v-if="item.activity">
								<td class="blue">
									План
								</td>
								<td>
									<div>
										<b>Активность: {{ item.activity.name }}</b>
									</div>
									<div>{{ item.plan }}</div>
								</td>
							</tr>
							<tr>
								<td class="blue">
									Условия
								</td>
								<td>{{ item.text }}</td>
							</tr>
						</table>
					</template>
				</b-tab>
			</b-tabs>
			<p
				class="font-16 text-muted mt-3"
				v-else
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

export default {
	name: 'PopupQuartal',
	components: {
		DateSelect,
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
	},
	watch: {
		selectedDate(){
			this.$nextTick(() => {
				this.fetchBefore()
			})
		}
	},
	created(){
		this.setMonth()
		this.fetchBefore()
	},
	methods: {
		...mapActions(useProfileSalaryStore, ['setReadedPremiums']),
		/**
         * set month
         */
		setMonth() {
			let year = this.$moment().format('YYYY')
			this.dateInfo.currentMonth = this.dateInfo.currentMonth ? this.dateInfo.currentMonth : this.$moment().format('MMMM')
			// this.currentMonth = this.dateInfo.currentMonth;
			this.dateInfo.date = `${this.dateInfo.currentMonth} ${year}`

			let currentMonth = this.$moment(this.dateInfo.currentMonth, 'MMMM')

			//Расчет выходных дней
			this.dateInfo.monthEnd = currentMonth.endOf('month'); //Конец месяца
			this.dateInfo.weekDays = currentMonth.weekdayCalc(this.dateInfo.monthEnd, [6]) //Колличество выходных
			this.dateInfo.daysInMonth = currentMonth.daysInMonth() //Колличество дней в месяце
			this.dateInfo.workDays = this.dateInfo.daysInMonth - this.dateInfo.weekDays //Колличество рабочих дней
		},

		fetchBefore() {
			this.fetchData({
				data_from: {
					year: this.currentYear,
					month: this.$moment(this.currentMonth, 'MMMM').format('M')
				},
				user_id: this.$laravel.userId
			})
		},

		fetchData(filters = null) {
			this.loading = true

			this.axios.post('/statistics/quartal-premiums', {
				filters: filters
			}).then(response => {
				if(response.data){
					this.itemsUser = response.data[0];
					this.itemsGroup = response.data[1];
					this.itemsPosition = response.data[2];
				}
				// this.defineSourcesAndGroups('t');

				// this.items.forEach(el => el.expanded = true);

				this.setReadedPremiums()
				this.loading = false
			}).catch(error => {
				this.loading = false
				alert(error)
			});
		},

		defineSourcesAndGroups() {
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
