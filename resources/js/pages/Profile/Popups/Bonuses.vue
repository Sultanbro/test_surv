<template>
	<div
		class="PopupBonuses popup__content mt-5"
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
									{{ $moment.utc(new Date(item.date)).local().format('DD.MM.YYYY') }}
								</td>
								<td class="text-center">
									{{ item.sum }}
								</td>
								<td class="fz14">
									<BonusComments
										v-if="item.manual"
										:items="[item.manual]"
									/>
									<BonusComments
										v-if="item.bonuses"
										:items="item.bonuses"
									/>
									<BonusComments
										v-if="item.test"
										:items="item.test"
									/>
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
import BonusComments from './BonusComments.vue'

export default {
	name: 'PopupBonuses',
	components: {
		DateSelect,
		ProfileTabs,
		BonusComments,
	},
	props: {},
	data: function () {
		return {
			bonuses: [],
			dateInfo: {
				currentMonth: null,
				monthEnd: 0,
				workDays: 0,
				weekDays: 0,
				daysInMonth: 0
			},
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

		setMonth() {
			let year = this.$moment().format('YYYY')
			this.dateInfo.currentMonth = this.dateInfo.currentMonth ? this.dateInfo.currentMonth : this.$moment().format('MMMM')
			this.dateInfo.date = `${this.dateInfo.currentMonth} ${year}`

			let currentMonth = this.$moment(this.dateInfo.currentMonth, 'MMMM')

			//Расчет выходных дней
			this.dateInfo.monthEnd = currentMonth.endOf('month'); //Конец месяца
			this.dateInfo.weekDays = currentMonth.weekdayCalc(this.dateInfo.monthEnd, [6]) //Колличество выходных
			this.dateInfo.daysInMonth = currentMonth.daysInMonth() //Колличество дней в месяце
			this.dateInfo.workDays = this.dateInfo.daysInMonth - this.dateInfo.weekDays //Колличество рабочих дней
		},

		// Заработанные бонусы
		async fetchData() {
			this.loading = true

			const {data} = await this.axios.post('/bonuses', {
				month: this.$moment(this.currentMonth, 'MMMM').format('M'),
				year: this.currentYear,
			})

			this.history = data.data.history
			this.loading = false
		},

		// Можно заработать
		async fetchBonuses(){
			this.loading = true
			const {data} = await this.axios.post('/bonus/user')
			this.bonuses = data.bonuses
			this.setReadedBonuses()
			this.loading = false
		}
	}
};
</script>

<style lang="scss">
.PopupBonuses{
	&-reasons{
		white-space: pre-wrap;
	}
}
</style>
