<template>
	<div
		class="PopupBalance popup__content  mt-3"
		:class="{'v-loading': loading}"
	>
		<div class="popup__filter pb-4">
			<div class="popup__filter-title">
				Ваши начисления за период работы
			</div>

			<DateSelect
				v-model="selectedDate"
				class="ml-a Balance-datePicker"
			/>
		</div>
		<div class="balance__content custom-scroll">
			<table class="balance__table">
				<thead>
					<tr>
						<th
							v-for="field in fields"
							:key="field.key"
							:class="{
								'text-center': field.key != '0',
								'SalaryCell-weekend-0': field.weekend
							}"
						>
							{{ field.label }}
							<i
								v-if="field.key == 'avanses'"
								v-b-popover.hover.right.html="'Авансы отмечены зеленым'"
								class="fa fa-info-circle"
							/>
							<i
								v-if="field.key == 'fines'"
								v-b-popover.hover.right.html="'Депримирование отмечено красным'"
								class="fa fa-info-circle"
							/>
						</th>
					</tr>
				</thead>
				<tbody>
					<tr
						v-for="(item, index) in items"
						:key="index"
					>
						<td
							v-for="field in fields"
							:key="field.key"
							:class="[{
								'text-center': field.key,
								'balance__table-day': parseInt(field.key) > 0,
								'SalaryCell': item[field.key] && item[field.key].dayType,
							}, item[field.key] ? `SalaryCell${field.weekend ? '-weekend' : ''}-${item[field.key].dayType || '0'}` : '']"
							@click="showHistory(field.key)"
						>
							<template v-if="item[field.key] !== undefined">
								{{ item[field.key].value }}
							</template>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<!--  history  -->
		<div
			v-if="history"
			ref="historyElement"
		>
			<div class="balance__inner">
				<div class="balance__title">
					История за {{ currentDay }} {{ currentMonth }}
				</div>

				<BalanceItem title="Начислено">
					<div v-if="Number(history.value) > 0">
						{{ history.value }}
					</div>
					<div v-else>
						0
					</div>
					<template #footer>
						<div v-if="history.training">
							<h6>Стажировка</h6>
							<p>Может быть пол суммы</p>
						</div>
					</template>
				</BalanceItem>

				<BalanceItem title="Депремирование">
					<template v-for="(item, index) in history.fines">
						<p :key="index">
							{{ item.name }}
						</p>
					</template>
					<p v-if="history.fines.length == 0">
						Нет депремирований
					</p>
				</BalanceItem>

				<BalanceItem title="Бонусы">
					<div class="mb-9">
						<template v-for="(item, index) in history.bonuses">
							<div :key="index">
								<div>
									<b>
										{{ item.bonus }} KZT
									</b>
								</div>
								<div>
									{{ item.comment_bonus }}
								</div>
							</div>
						</template>
						<div v-if="history.bonuses.length == 0 && history.awards.length == 0 && history.test_bonus.length == 0">
							Нет бонусов
						</div>
					</div>
					<div
						v-if="history.awards.length != 0"
						class="mb-5"
					>
						<template v-for="(item, index) in history.awards">
							<div :key="index">
								<div>
									<b>
										{{ item.amount }} KZT
									</b>
								</div>
								<div>
									{{ item.comment }}
								</div>
							</div>
						</template>
					</div>
					<div
						v-if="history.test_bonus.length != 0"
						class="mb-5"
					>
						<div>
							За пройденные тесты
						</div>
						<template v-for="(item, index) in history.test_bonus">
							<div :key="index">
								<div>
									<b>
										{{ item.amount }} KZT
									</b>
								</div>
								<div>
									{{ item.comment }}
								</div>
							</div>
						</template>
					</div>
				</BalanceItem>

				<BalanceItem title="Авансы">
					<template v-for="(item, index) in history.avanses">
						<div :key="index">
							<div>
								<b>
									{{ item.paid }} KZT
								</b>
							</div>
							<div>
								{{ item.comment_paid }}
							</div>
						</div>
					</template>
					<p v-if="history.avanses.length == 0">
						Нет авансов
					</p>
				</BalanceItem>
			</div>
		</div>
	</div>
</template>

<script>
import { mapState } from 'pinia'
import { usePortalStore } from '@/stores/Portal'
import BalanceItem from './BalanceItem'
import { useYearOptions } from '@/composables/yearOptions'
import DateSelect from '../DateSelect'
import salaryCellType from '@/composables/salaryCellType'

export default {
	name: 'PopupBalance',
	components: {
		BalanceItem,
		DateSelect,
	},
	props: {},
	data: function () {
		const now = new Date()
		return {
			data: {
				hours: [],
				salaries: [],
				times: [],
				total: {
					salaries: 0,
					hours: 0,
				}
			},
			totalFines: 0,
			total_avanses: 0,
			selectedDate: this.$moment().format('DD.MM.YYYY'),
			currentDay: now.getDate(),
			history: null,
			loading: false
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
		fields(){
			const fields = [
				{
					key: '0',
					label: 'Дни',
					variant: 'title',
					class: 'text-left t-name'
				},
				{
					key: 'total',
					label: 'К выдаче',
					variant: 'title',
					class: 'text-center t-name'
				},
				{
					key: 'avanses',
					label: 'Авансы',
					variant: 'title',
					class: 'text-center t-name'
				},
				{
					key: 'fines',
					label: 'Депремирования',
					variant: 'title',
					class: 'text-center t-name'
				}
			]

			for (let i = 1; i <= this.daysInMonth; ++i) {
				const dayName = this.$moment(`${i} ${this.currentMonth} ${this.currentYear}`, 'D MMMM YYYY').locale('en').format('ddd')
				fields.push({
					key: `${i}`,
					label: `${i}`,
					sortable: false,
					weekend: dayName === 'Sat' || dayName === 'Sun',
				})
			}
			return fields
		},
		daysInMonth(){
			return this.$moment(`${this.currentMonth} ${this.currentYear}`, 'MMMM YYYY').daysInMonth()
		},
		items(){
			const totalSalary = Number(this.data.total.salaries) - Number(this.totalFines) - Number(this.total_avanses)
			return [
				{
					...this.data.times,
					0: {value: 'Время прихода'},
				},
				{
					...this.data.salaries,
					0: {value: 'Начисления'},
					total: {value: Number(totalSalary).toFixed(0)},
					avanses: {value: Number(this.total_avanses).toFixed(0)},
					fines: {value: Number(this.totalFines).toFixed(0)},
				},
				{
					...this.data.hours,
					0: {value: 'Отработанные часы'},
					total: {value: Number(this.data.total.hours).toFixed(1)}
				},
			]
		}
	},
	watch: {
		selectedDate(){
			this.$nextTick(() => {
				this.fetchData()
			})
		}
	},
	created() {
		this.fetchData()
	},
	methods: {
		showHistory(day = 0) {
			if([
				'0',
				'total',
				'avanses',
				'fines'
			].includes(day)) return;

			if(day != 0) this.currentDay = day

			const data = this.data.salaries[this.currentDay]

			this.history = {
				fines: data.fines,
				avanses: data.avanses,
				bonuses: data.bonuses,
				test_bonus: data.test_bonus,
				awards: data.awards,
				training: data.training,
				value: data.value,
				calculated: data.calculated
			}

			if(day){
				setTimeout(() => {
					this.$refs.historyElement.scrollIntoView({ behavior: 'smooth' })
				}, 1)
			}
		},
		hideHistory(){
			this.history = null
		},

		/**
         * Загрузка данных для таблицы
         */
		fetchData() {
			this.loading = true

			this.axios.post('/timetracking/zarplata-table-new', {
				month: this.$moment(this.currentMonth, 'MMMM').format('M'),
				year: this.currentYear
			}).then(({data}) => {
				this.data = this.prepareData(data.data)
				this.totalFines = data.totalFines
				this.total_avanses = data.total_avanses

				this.showHistory()
				this.loading = false
			}).catch(e => console.error(e))
		},

		prepareData({salaries, hours, times}){
			const total = {
				salaries: 0,
				hours: 0,
			}

			for (let day of Object.keys(salaries)){
				const hasBonus = salaries[day].bonuses && salaries[day].bonuses.length
				const hasAwards = salaries[day].awards && salaries[day].awards.length
				const hasTestBonus = salaries[day].test_bonus && salaries[day].test_bonus.length
				const hasFine = salaries[day].fines && salaries[day].fines.length ? salaryCellType.FINE : 0
				const hasAvans = salaries[day].avanses && salaries[day].avanses.length ? salaryCellType.AVANS : 0
				const hasBonuses = hasBonus || hasAwards || hasTestBonus ? salaryCellType.BONUS : 0
				const isTraning = salaries[day].training ? salaryCellType.TRAINING : 0

				salaries[day].dayType = isTraning | hasBonuses | hasAvans | hasFine

				let val = Number(salaries[day].value);
				total.salaries += isNaN(val) ? 0 : val;
			}

			for (let day of Object.keys(times)){
				if(times[day].value)
					times[day].value = this.$moment.utc(times[day].value, 'hh:mm').local().format('hh:mm')
			}

			for (let day of Object.keys(hours)){
				let val = Number(hours[day].value);
				total.hours += isNaN(val) ? 0 : val;
			}

			return {salaries, hours, times, total}
		},
	}
};
</script>

<style lang="scss">
	$fine: #e84f71;
	$avans: #8bab00;
	$bonus: #8fc9ff;
	$training: #f90;

	.Balance{
		&-datePicker{
			min-width: 200px;
		}
	}

	.balance__content {
		overflow-x: auto;
		padding-bottom: 2rem;
	}

	.balance__table {
		width: 100%;
		margin: 4rem 0.1rem 0;
		border-spacing: 0;
		color: #62788B;

		td:first-child, th:first-child {
			text-align: left;
			padding-left: 1.5rem;
			padding-right: 1rem;
		}

		th {
			white-space: nowrap;
			font-weight: 600;
			max-width: 25.9rem;
			background: #f4f6fd;
			font-size: 1.3rem;
			height: 4rem;
			min-width: 4rem;
			padding: 0 1rem;
			color: #62788B;
			border: 1px solid #EBEDF5;

			&.Sat, &.Sun {
				background: #fef2cb;
			}
		}

		th:first-child {
			border-radius: 1.2rem 0 0 0;
			border: none;
			outline: 1px solid #EBEDF5;
		}

		th:last-child {
			border-radius: 0 1.2rem 0 0;
			border: none;
			outline: 1px solid #EBEDF5;
		}

		td {
			min-width: 4rem;
			max-width: 25.9rem;
			border: 1px solid #EBEDF5;
			height: 4rem;
			font-size: 1.2rem;
			font-family: "Inter", sans-serif;
			text-align: center;

			&.yellow {
				background: #FFEF86;
			}

			&.Sat, &.Sun {
				background: #fef2cb;
			}

			&.orange {
				background: #FF9900;
				color: #fff;
			}

			&.blue {
				background: #8FC9FF;
			}

			&.pink {
				background: #EC6898;
				color: #fff;
			}

			&.green-pink {
				background: linear-gradient(105deg, #8BAB00 40%, #EC6898 40%);
			}
		}
	}

	.balance__table-day {
		cursor: pointer;
	}

	.balance__title {
		margin-top: 0rem;
		margin-bottom: 3rem;
		font-family: "Open Sans", sans-serif;
		font-size: 2rem;
		color: rgb(139 171 2);
		font-weight: 400;
		border-bottom: 1px solid #aebde0;
		padding-bottom: 1rem;

		span {
			display: block;
		}
	}

	.balance__inner {
		padding: 4rem 4rem;
		margin-top: 2rem;
		margin-bottom: 2rem;
		// display: flex;
		background: #F5F7FC;
		border-radius: 1.5rem;
		justify-content: space-between;
	}

	.fz-09 {
		font-size: 0.9rem;
	}

	@media(max-width: 1200px) {
		.balance__inner {
			width: 90rem;
		}
	}

	@media(max-width: 779px) {
		.balance .select-css {
			max-width: 18rem;
		}
	}

	@media(max-width: 500px) {
		.balance__title {
			font-size: 2.7rem
		}
	}


</style>
