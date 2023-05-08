<template>
	<div
		class="popup__content  mt-5"
		:class="{'v-loading': loading}"
	>
		<div class="tabs">
			<div class="popup__filter pb-4">
				<DateSelect
					v-model="selectedDate"
					class="ml-a Balance-datePicker"
				/>
			</div>

			<div class="tabs">
				<div class="tabs__wrapper custom-scroll">
					<div class="trainee__tabs tabs__wrapper">
						<div
							class="trainee__tab tab__item is-active"
							onclick="switchTabs(this)"
							data-index="1"
						>
							Заработанные бонусы
						</div>
						<div
							class="trainee__tab tab__item"
							onclick="switchTabs(this)"
							data-index="2"
						>
							Можно заработать
						</div>
					</div>
					<select class="select-css trainee-select mobile-select">
						<option value="1">
							Заработанные бонусы
						</option>
						<option value="2">
							Можно заработать
						</option>
					</select>
				</div>

				<div class="tab__content">
					<div
						class="kaspi__content custom-scroll-y tab__content-item is-active"
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
					<div
						class="kaspi__content custom-scroll-y tab__content-item"
						data-content="2"
					>
						<!--                <div class="kaspi__wrapper">-->
						<!--                    -->
						<!--                    <div v-html="potential_bonuses"></div>-->
						<!--                </div>-->
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
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import { mapState } from 'pinia'
import { usePortalStore } from '@/stores/Portal'
import { useYearOptions } from '@/composables/yearOptions'
import DateSelect from '../DateSelect'

export default {
	name: 'PopupBonuses',
	components: {
		DateSelect,
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
		/**
         * set month
         */
		setMonth() {
			let year = this.$moment().format('YYYY')
			this.dateInfo.currentMonth = this.dateInfo.currentMonth ? this.dateInfo.currentMonth : this.$moment().format('MMMM')
			this.currentMonth = this.dateInfo.currentMonth;
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
