<template>
	<div class="mt-3">
		<div
			:key="myTable"
			class="mb-0"
		>
			<b-table
				v-if="dataLoaded"
				id="tabelTable"
				responsive
				striped
				:sticky-header="true"
				class="text-nowrap text-right my-table mb-0"
				:small="true"
				:bordered="true"
				:items="items"
				:fields="fields"
				show-empty
				empty-text="Нет данных"
			>
				<template #head(avanses)>
					<i
						v-b-popover.hover.right.html="'Авансы отмечены зеленым'"
						class="fa fa-info-circle"
					/>
				</template>
				<template #head(fines)>
					<i
						v-b-popover.hover.right.html="'Депримирование отмечено красным'"
						class="fa fa-info-circle"
					/>
				</template>
				<template #cell(avanses)="avansesData">
					<div v-if="avansesData.index == 1">
						{{ avansesData.value.value }}
					</div>
				</template>
				<template #cell(fines)="finesData">
					<div v-if="finesData.index == 1">
						{{ finesData.value.value }}
					</div>
				</template>
				<template #cell()="cellData">
					<div
						:class="{
							'day-fine':cellData.value.hasFine,
							'day-training':cellData.value.training,
							'day-avans': cellData.value.hasAvans,
							'day-bonus':cellData.value.hasBonus,
						}"
						@click="openDay(cellData.value)"
					>
						{{ cellData.value.value }}
					</div>
				</template>
			</b-table>
		</div>
		<sidebar
			v-if="openSidebar"
			:title="sidebarTitle"
			:open="openSidebar"
			width="350px"
			@close="openSidebar=false"
		>
			<h6 class="mt-3">
				Начислено
			</h6>
			<div class="mb-5">
				<div v-if="Number(sidebarContent.value) > 0">
					{{ sidebarContent.calculated }}
				</div>
				<div v-else>
					0
				</div>
			</div>
			<div v-if="sidebarContent.training">
				<h6>Стажировка</h6>
				<p>Может быть пол суммы</p>
			</div>
			<h6 class="mt-3">
				Депримирование
			</h6>
			<div class="mb-5">
				<p
					v-for="item in sidebarContent.fines"
					:key="item"
				>
					{{ item.name }}
				</p>
				<p v-if="sidebarContent.fines.length == 0">
					Нет штрафов
				</p>
			</div>
			<h6 class="mt-3">
				Бонусы
			</h6>
			<div class="mb-5">
				<div
					v-for="item in sidebarContent.bonuses"
					:key="item"
				>
					<div>
						<b>
							{{ item.bonus }} KZT
						</b>
					</div>
					<div>
						{{ item.comment_bonus }}
					</div>
				</div>
				<div v-if="sidebarContent.bonuses.length == 0 && sidebarContent.awards.length == 0 && sidebarContent.test_bonus.length == 0">
					Нет бонусов
				</div>
			</div>
			<div
				v-if="sidebarContent.awards.length != 0"
				class="mb-5"
			>
				<div
					v-for="item in sidebarContent.awards"
					:key="item"
				>
					<div>
						<b>
							{{ item.amount }} KZT
						</b>
					</div>
					<div>
						{{ item.comment }}
					</div>
				</div>
			</div>
			<div
				v-if="sidebarContent.test_bonus.length != 0"
				class="mb-5"
			>
				<div>
					За пройденные тесты
				</div>
				<div
					v-for="item in sidebarContent.test_bonus"
					:key="item"
				>
					<div>
						<b>
							{{ item.amount }} KZT
						</b>
					</div>
					<div>
						{{ item.comment }}
					</div>
				</div>
			</div>
			<h6 class="mt-3">
				Авансы
			</h6>
			<div class="mb-5">
				<div
					v-for="item in sidebarContent.avanses"
					:key="item"
				>
					<div>
						<b>
							{{ item.paid }} KZT
						</b>
					</div>
					<div>
						{{ item.comment_paid }}
					</div>
				</div>
				<p v-if="sidebarContent.avanses.length == 0">
					Нет авансов
				</p>
			</div>
		</sidebar>
	</div>
</template>

<script>
/* eslint-disable camelcase */

export default {
	name: 'TableUserSalary',
	props: {
		activeuserid: {
			type: Number,
			default: 0
		},
		date: {
			type: Number,
			default: 0
		},
		month: {
			type: String,
			default: ''
		}
	},

	data() {
		return {
			data: {},
			openSidebar: false,
			sidebarContent: {},
			sidebarTitle: 'История',
			totalFines: 0,
			total_avanses: 0,
			items: {
				salaries: [],
				times: [],
				hours: []
			},
			fields: [],
			dayInfoText: '',
			hasPremission: false,
			dateInfo: {
				currentMonth: null,
				monthEnd: 0,
				workDays: 0,
				weekDays: 0,
				daysInMonth: 0
			},
			dataLoaded: false,
			myTable: 1
		}
	},

	watch: {
		month: {
			handler: function (val) {
				this.dateInfo.currentMonth = val
				this.fetchData()
			},
		},
	},

	created() {
		// //Текущая группа
		this.setMonth()
		this.setFields()
		this.fetchData()
	},
	methods: {

		//Установка заголовока таблицы
		setFields() {
			let fields = []

			fields = [
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
					label: 'Штрафы',
					variant: 'title',
					class: 'text-center t-name'
				}
			];

			let days = this.dateInfo.daysInMonth

			for (let i = 1; i <= days; i++) {
				let dayName = this.$moment(`${i} ${this.dateInfo.date}`, 'D MMMM YYYY').locale('en').format('ddd')
				fields.push({
					key: `${i}`,
					label: `${i}`,
					sortable: false,
					class: `day ${dayName}`,
				})
			}
			this.fields = fields
		},

		//Загрузка данных для таблицы
		fetchData() {
			let loader = this.$loading.show();

			this.axios.post('/timetracking/zarplata-table-new', {
				month: this.$moment(this.month, 'MMMM').format('M'),
			}).then(response => {

				this.myTable++

				this.setMonth()
				this.setFields()

				this.data = response.data.data
				this.totalFines = response.data.totalFines
				this.total_avanses = response.data.total_avanses


				this.loadItems()
				this.dataLoaded = true
				this.myTable++


				loader.hide()
			})
		},

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

		//Добавление загруженных данных в таблицу
		loadItems() {
			let items = [];
			let temp = [];
			let total = {
				'salaries':0,
				'hours':0,
			};

			for (let key in this.data) {
				temp[key] = []
				for (let keyt in this.data[key]) {
					temp[key][keyt] = ({
						'value': this.data[key][keyt]['value'],
						'fines': this.data[key][keyt]['fines'],
						'avanses': this.data[key][keyt]['avanses'],
						'bonuses': this.data[key][keyt]['bonuses'],
						'test_bonus': this.data[key][keyt]['test_bonus'],
						'awards': this.data[key][keyt]['awards'],
						'hasFine': this.data[key][keyt]['fines'] !== undefined && this.data[key][keyt]['fines'].length,
						'hasBonus': (this.data[key][keyt]['bonuses'] !== undefined && this.data[key][keyt]['bonuses'].length) || (this.data[key][keyt]['awards'] !== undefined && this.data[key][keyt]['awards'].length)
                            || (this.data[key][keyt]['test_bonus'] !== undefined && this.data[key][keyt]['test_bonus'].length),
						'hasAvans': this.data[key][keyt]['avanses'] !== undefined && this.data[key][keyt]['avanses'].length,
						'training': this.data[key][keyt]['training'],
					})

					if(key == 'salaries' || key == 'hours') {
						let val = Number(this.data[key][keyt]['value']);
						total[key] += isNaN(val) ? 0 : val;
					}
				}
			}

			temp['salaries'][0] = {
				'value': 'Начисления',
			};

			let total_salary = 0;
			total_salary = Number(total['salaries']) - Number(this.totalFines) - Number(this.total_avanses);

			temp['salaries']['total'] = {
				'value': Number(total_salary).toFixed(0),
			};
			temp['salaries']['avanses'] = {
				'value': Number(this.total_avanses).toFixed(0)
			};
			temp['salaries']['fines'] = {
				'value': Number(this.totalFines).toFixed(0)
			};


			temp['times'][0] = {
				'value': 'Время прихода',
			};
			temp['hours'][0] = {
				'value': 'Отработанные часы',
			};
			temp['hours']['total'] = {
				'value': Number(total['hours']).toFixed(1),
			};
			temp['times']['avanses'] = {
				'value': 0
			};
			temp['times']['fines']= {
				'value': 0
			};
			temp['hours']['avanses'] = {
				'value': 0
			};
			temp['hours']['fines'] = {
				'value': 0
			};
			items.push(temp['times'])
			items.push(temp['salaries'])
			items.push(temp['hours'])
			this.items = items
		},
		openDay(data) {
			this.openSidebar = true
			this.sidebarContent = {
				fines: data.fines,
				avanses: data.avanses,
				bonuses: data.bonuses,
				test_bonus: data.test_bonus,
				awards: data.awards,
				training: data.training,
			}
		}
	}
}
</script>

<style lang="scss">


.b-table-sticky-header {
    max-height: 450px;
}

.table-day-1 {
    color: rgb(0, 0, 0);
    background: rgb(204, 204, 204);
}

.table-day-2 {
    color: #fff;
    background: blue;
}

.table-day-3 {
    color: rgb(0, 0, 0);
    background: aqua;
}

.table-day-4 {
    color: rgb(0, 0, 0);
    background: rgb(200, 162, 200);
}

.table-day-5 {
    color: rgb(0, 0, 0);
    background: orange;
}




.my-table-max {
    max-height: inherit !important;

    .day {
        padding: 0 !important;
        text-align: center;

        &.Sat,
        &.Sun {
            background-color: #FEF2CB;
        }

        &.table-danger {
            background-color: #f5c6cb !important;
        }
    }



}

.cell-input {
    background: none;
    border: none;
    text-align: center;
    -moz-appearance: textfield;
    font-size: .8rem;
    font-weight: normal;
    padding: 0;
    color: #000;
    border-radius: 0;

    &:focus {
        outline: none;
    }

    &::-webkit-outer-spin-button,
    &::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
}

</style>
