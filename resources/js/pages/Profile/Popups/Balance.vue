<template>
	<div
		class="popup__content  mt-3"
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
							:class="{
								'text-center': field.key != '0',
								[field.class]: field.class !== undefined
							}"
							:key="field.key"
						>
							{{ field.label }}
							<i
								v-if="field.key == 'avanses'"
								class="fa fa-info-circle"
								v-b-popover.hover.right.html="'Авансы отмечены зеленым'"
							/>
							<i
								v-if="field.key == 'fines'"
								class="fa fa-info-circle"
								v-b-popover.hover.right.html="'Депримирование отмечено красным'"
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
							@click="showHistory(field.key)"
							:class="{
								'day-fine': item[field.key] !== undefined && item[field.key].hasFine,
								'day-avans': item[field.key] !== undefined && item[field.key].hasAvans,
								'day-bonus': item[field.key] !== undefined && item[field.key].hasBonus,
								'text-center': field.key != '0',
								'day-training': item[field.key] !== undefined && item[field.key].training,
								'balance__table-day': parseInt(field.key) > 0,
								[field.class]: field.class !== undefined
							}"
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
					История за {{ this.currentDay }} {{ this.currentMonth }}
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
			data: [],
			items: [],
			totalFines: null,
			total_avanses: null,
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
					class: `day ${dayName}`,
				})
			}
			return fields
		},
		daysInMonth(){
			return this.$moment(`${this.currentMonth} ${this.currentYear}`, 'MMMM YYYY').daysInMonth()
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
			}).then(response => {
				this.data = response.data.data
				this.totalFines = response.data.totalFines
				this.total_avanses = response.data.total_avanses

				this.loadItems()

				this.showHistory()
				this.loading = false
			}).catch(e => console.error(e))
		},

		loadItems() {
			const items = [];
			const temp = [];
			const total = {
				salaries: 0,
				hours: 0,
			};

			for (let key in this.data) {
				temp[key] = []
				for (let keyt in this.data[key]) {
					const hasBonus = this.data[key][keyt]['bonuses'] !== undefined && this.data[key][keyt]['bonuses'].length
					const hasAwards = this.data[key][keyt]['awards'] !== undefined && this.data[key][keyt]['awards'].length
					const hasTestBonus = this.data[key][keyt]['test_bonus'] !== undefined && this.data[key][keyt]['test_bonus'].length
					const hasFine = this.data[key][keyt]['fines'] !== undefined && this.data[key][keyt]['fines'].length
					const hasAvans = this.data[key][keyt]['avanses'] !== undefined && this.data[key][keyt]['avanses'].length
					temp[key][keyt] = ({
						value: this.data[key][keyt]['value'],
						fines: this.data[key][keyt]['fines'],
						avanses: this.data[key][keyt]['avanses'],
						bonuses: this.data[key][keyt]['bonuses'],
						test_bonus: this.data[key][keyt]['test_bonus'],
						awards: this.data[key][keyt]['awards'],
						hasFine,
						hasBonus: hasBonus || hasAwards || hasTestBonus,
						hasAvans,
						training: this.data[key][keyt]['training'],
					})

					if(key === 'times' && temp[key][keyt].value){
						temp[key][keyt].value = this.$moment.utc(temp[key][keyt].value, 'hh:mm').local().format('hh:mm')
					}

					if(key == 'salaries' || key == 'hours') {
						let val = Number(this.data[key][keyt].value);
						total[key] += isNaN(val) ? 0 : val;
					}
				}
			}

			temp['salaries'][0] = {
				value: 'Начисления',
			};

			const total_salary = Number(total['salaries']) - Number(this.totalFines) - Number(this.total_avanses)

			temp['salaries']['total'] = {
				value: Number(total_salary).toFixed(0),
			};
			temp['salaries']['avanses'] = {
				value: Number(this.total_avanses).toFixed(0)
			};
			temp['salaries']['fines'] = {
				value: Number(this.totalFines).toFixed(0)
			};


			temp['times'][0] = {
				value: 'Время прихода',
			};
			temp['hours'][0] = {
				value: 'Отработанные часы',
			};
			temp['hours']['total'] = {
				value: Number(total['hours']).toFixed(1),
			};
			temp['times']['avanses'] = {
				value: 0
			};
			temp['times']['fines']= {
				value: 0
			};
			temp['hours']['avanses'] = {
				value: 0
			};
			temp['hours']['fines'] = {
				value: 0
			};
			items.push(temp['times'])
			items.push(temp['salaries'])
			items.push(temp['hours'])
			this.items = items
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
			color: #62788B;
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

	.fine, .avans, .bonus {
		color: #fff;
	}

	.balance__table {
		.day-fine {
			background: $fine;
			color: #fff;
		}

		.day-training {
			background: $training;
			color: #fff;

			&.day-fine {
				background: linear-gradient(110deg, $training 50%, $fine 50%);
			}
		}

		.day-bonus {
			background: $bonus;
			color: #fff;

			&.day-fine {
				background: linear-gradient(110deg, $bonus 50%, $fine 50%);
			}

			&.day-training {
				background: linear-gradient(110deg, $bonus 50%, $training 50%);
			}

			&.day-fine.day-training {
				background: linear-gradient(110deg, $bonus 33%, transparent 33%), linear-gradient(110deg, $fine 66%, $training 66%);
			}
		}

		.day-avans {
			background: $avans;
			color: #fff;

			&.day-fine {
				background: linear-gradient(110deg, $avans 50%, $fine 50%);
			}

			&.day-bonus {
				background: linear-gradient(110deg, $avans 50%, $bonus 50%);
			}

			&.day-training {
				background: linear-gradient(110deg, $avans 50%, $training 50%);
			}

			&.day-bonus.day-fine {
				background: linear-gradient(110deg, $avans 33%, transparent 33%), linear-gradient(110deg, $bonus 66%, $fine 66%);
			}

			&.day-bonus.day-fine.day-training {
				background: linear-gradient(110deg, $avans 25%, transparent 25%), linear-gradient(110deg, $bonus 50%, $fine 50%), linear-gradient(110deg, $fine 75%, $training 75%);
			}
		}
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
