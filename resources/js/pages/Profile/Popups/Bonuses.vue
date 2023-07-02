<template>
	<div
		class="popup__content  mt-5"
		:class="{'v-loading': loading}"
	>
		<div class="popup__filter pb-4">
			<DateSelect
				v-model="selectedDate"
				class="ml-a Balance-datePicker"
			/>
		</div>
		<ProfileTabs :tabs="['Заработанные бонусы', 'Можно заработать']">
			<template #tab(0)>
				<div
					class="kaspi__content custom-scroll-y"
					data-content="1"
				>
					<table>
						<thead>
							<tr>
								<th class="text-center">
									Дата
								</th>
								<th class="text-center">
									Сумма
								</th>
								<th class="text-center">
									Комментарии
								</th>
							</tr>
						</thead>
						<tbody>
							<tr
								v-for="(item, index) in history"
								:key="index"
							>
								<td class="text-center">
									{{ $moment.utc(new Date(item.date)).local().format('DD.MM.YYYY HH:mm') }}
								</td>
								<td class="text-center">
									{{ item.sum }}
								</td>
								<td>
									<p class="kaspi__content-comment fz14 mb-0">
										{{ item.comment }}
									</p>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</template>
			<template #tab(1)>
				<div class="kaspi__content custom-scroll-y">
					<b-tabs>
						<b-tab
							v-for=" (bonus, i) in bonuses"
							:key="i"
							:title="bonus.name"
						>
							<table>
								<thead>
									<tr>
										<th class="text-center">
											Название
										</th>
										<th class="text-center">
											За что
										</th>
										<th class="text-center">
											Сумма
										</th>
									</tr>
								</thead>
								<tbody>
									<tr
										v-for="(item, index) in bonus.items"
										:key="index"
									>
										<td class="text-center">
											{{ item.title }}
										</td>
										<td class="text-center">
											{{ item.text }}
										</td>
										<td class="text-center">
											{{ item.sum }}
										</td>
									</tr>
								</tbody>
							</table>
						</b-tab>
					</b-tabs>
				</div>
			</template>
		</ProfileTabs>
	</div>
</template>

<script>
import { mapState, mapActions } from 'pinia'
import { usePortalStore } from '@/stores/Portal'
import { useProfileSalaryStore } from '@/stores/ProfileSalary'
import { useYearOptions } from '@/composables/yearOptions'
import DateSelect from '../DateSelect'
import ProfileTabs from '@ui/ProfileTabs'

export default {
	name: 'PopupBonuses',
	components: {
		DateSelect,
		ProfileTabs,
	},
	props: {},
	data: function () {
		return {
			fields: [],
			bonuses: [],
			dateInfo: {
				currentMonth: null,
				monthEnd: 0,
				workDays: 0,
				weekDays: 0,
				daysInMonth: 0
			},
			potential_bonuses: '',
			history: [],
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
				this.fetchData()
			})
		}
	},
	created(){
		this.setMonth()
		this.fetchData()
		this.fetchBonuses()
	},
	methods: {
		...mapActions(useProfileSalaryStore, ['setReadedBonuses']),
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

		fetchData() {
			this.loading = true

			this.axios
				.post('/bonuses', {
					month: this.$moment(this.currentMonth, 'MMMM').format('M'),
					year: this.currentYear,
				})
				.then((response) => {
					// this.potential_bonuses = response.data.data.potential_bonuses
					this.history = response.data.data.history

					this.loading = false
				});
		},
		fetchBonuses(){
			this.loading = true
			const _this = this;
			this.axios
				.post('/bonus/user')
				.then((response) => {
					_this.bonuses = response.data.bonuses
					this.setReadedBonuses()
					this.loading = false
				});
		}
	}
};
</script>

<style lang="scss">
.kaspi__content-comment{
    white-space: pre-wrap;
}
.kaspi__content-subtitle{
    padding-top: 2rem;
    font-size: 1.6rem;
    color: #3a3a3a;
    line-height: normal;
}
</style>
