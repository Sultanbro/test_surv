<template>
	<div class="mb-3">
		<h4 class="d-flex align-items-center justify-content-between">
			<div class="mr-2">
				Декомпозиция на месяц
			</div>
		</h4>

		<div class="table-container">
			<table class="table table-bordered table-responsive strt">
				<thead>
					<tr>
						<th class="b-table-sticky-column text-left">
							<div class="wd" />
						</th>
						<th
							class="text-center px-1 border-r-2"
							colspan="2"
						>
							<b>Итого</b>
						</th>
						<th
							class="text-center px-1 border-r-2"
							:class="{
								'weekend' : is_weekday[day],
							}"
							colspan="2"
							v-for="day in month.daysInMonth"
							:key="day"
						>
							<div>{{ day }}</div>
						</th>
					</tr>
					<tr>
						<th class="b-table-sticky-column text-left" />
						<th>план</th>
						<th>факт</th>
						<template v-for="day in month.daysInMonth">
							<th :key="day">
								план
							</th>
							<th
								:key="day + 'a'"
								class="border-r-2"
							>
								факт
							</th>
						</template>
					</tr>
				</thead>
				<tbody>
					<tr
						v-for="(item, index) in items"
						:key="index"
					>
						<td
							class="b-table-sticky-column text-left px-2 t-name"
							:title="item.id"
						>
							<div class="wd d-flex align-items-center">
								<input
									type="text"
									v-model="item.name"
									class="TableDecomposition-inputName w-250 text-left"
									placeholder="напишите название активности"
									@change="updateSettings($event, item, index, 'name')"
								>
								<i
									class="fa fa-pencil pointer mr-2"
									@click="showModal(index)"
									title="Поставить план"
								/>
								<i
									class="fa fa-trash pointer"
									@click="deleteRecord(item.id, index)"
									title="Удалить строку"
								/>
							</div>
						</td>
						<td>{{ Number(item.total_plan).toFixed(0) }}</td>
						<td class="border-r-2">
							{{ Number(item.total_fact).toFixed(0) }}
						</td>
						<template v-for="day in month.daysInMonth">
							<td
								v-if="item.editable"
								:key="day"
								class="px-0 day-minute text-center"
								:class="{
									'weekend' : is_weekday[day],
								}"
							>
								<div>
									<input
										type="number"
										v-model="item[day].plan"
										@change="updateSettings($event, item, index, day)"
									>
								</div>
							</td>
							<td
								v-else
								:key="day + 'a'"
								@click="editMode(item)"
								class="px-0 day-minute text-center"
								:class="{
									'weekend' : is_weekday[day],
								}"
							>
								<div>{{ item[day].plan }}</div>
							</td>
							<td
								v-if="item.editable"
								:key="day + 'b'"
								class="px-0 day-minute text-center border-r-2"
								:class="{
									'table-danger': Number(item[day].fact) != 0 && Number(item[day].plan) > Number(item[day].fact),
									'table-success': Number(item[day].fact) != 0 && Number(item[day].plan) <= Number(item[day].fact),
									'weekend' : is_weekday[day],
								}"
							>
								<div>
									<input
										type="number"
										v-model="item[day].fact"
										@change="updateSettings($event, item, index, day)"
									>
								</div>
							</td>
							<td
								v-else
								:key="day + 'c'"
								@click="editMode(item)"
								class="px-0 day-minute text-center border-r-2"
								:class="{
									'table-danger': Number(item[day].fact) != 0 && Number(item[day].plan) > Number(item[day].fact),
									'table-success': Number(item[day].fact) != 0 && Number(item[day].plan) <= Number(item[day].fact),
									'weekend' : is_weekday[day],
								}"
							>
								<div>{{ item[day].fact }}</div>
							</td>
						</template>
					</tr>
				</tbody>
			</table>
		</div>
		<button
			class="btn btn-success"
			@click="addRecord"
		>
			+ Добавить
		</button>



		<b-modal
			ref="change-plan-modal"
			hide-footer
			title="Проставить план"
		>
			<div class="row">
				<div class="col-6">
					<label for="">С:</label>
					<input
						type="number"
						v-model.number="planner.from"
						class="form-control form-control-sm mb-2"
						min="1"
						max="31"
					>
				</div>
				<div class="col-6">
					<label for="">По:</label>
					<input
						type="number"
						v-model.number="planner.to"
						class="form-control form-control-sm mb-2"
						min="1"
						max="31"
					>
				</div>
				<div class="col-6">
					<label for="">План:</label>
					<input
						type="number"
						v-model.number="planner.value"
						class="form-control form-control-sm mb-2"
						placeholder="План"
						min="1"
					>
				</div>
				<div class="col-12 mt-3">
					<b-button
						class="mt-3"
						variant="primary"
						block
						@click="changePlan"
					>
						Проставить
					</b-button>
				</div>
			</div>
		</b-modal>
	</div>
</template>

<script>
export default {
	name: 'TableDecomposition',
	props: {
		month: Object,
		data: Object,
	},
	data() {
		return {
			items: [],
			fields: [],
			itemsArray: [],
			records: [],
			is_weekday: {},
			planner: {
				value: null,
				from: 1,
				to: null,
				index: null,
			}
		};
	},
	watch: {
		data: function() {
			this.fetchData();
		},
	},
	created() {
		this.fetchData();

		this.getWeekdays()
		this.planner.to = this.month.daysInMonth
	},

	methods: {

		getWeekdays() {

			for (let i = 1; i <= this.month.daysInMonth; i++) {
				let day = i > 9 ? i : '0' + i;
				let month = Number(this.month.month) > 9 ? this.month.month : '0' + this.month.month;
				let date = new Date(this.month.currentYear + '-' + month  + '-' + day)
				if(date.getDay() == 0 || date.getDay() == 6) {
					this.is_weekday[i] = true
				} else {
					this.is_weekday[i] = false
				}
			}
		},

		fetchData() {
			let loader = this.$loading.show();

			this.records = this.data.records;
			this.accountsNumber = this.data.records.length

			this.calculateRecordsValues()
			//this.calcTotals()

			this.items = this.itemsArray;
			loader.hide();
		},

		addRecord() {
			let cells = {
				name:'',
				plan: 0,
				editable: false,
			};

			for (let i = 1; i <= this.month.daysInMonth; i++) {
				cells[i] = {
					'plan' : '',
					'fact' : '',
				}
			}

			this.items.push(cells)

			this.$toast.info('Пункт добавлен')
		},

		updateSettings(e, data, index) {
			data.editable = false
			this.updateTable(this.items);

			let post_data = {
				group_id: this.data.group_id,
				id: data.id,
				name: data.name,
				index: index,
				values: this.items[index],
			};

			this.reqSave(post_data);
		},

		reqSave(post_data) {
			let url = '/timetracking/analytics/decomposition/save';
			let loader = this.$loading.show();
			let self = this.items;
			let year = new Date().getFullYear();

			post_data.date = this.$moment(
				`${this.month.currentMonth} ${year}`,
				'MMMM YYYY'
			).format('YYYY-MM-DD');

			this.axios.post(url, post_data)
				.then((response) => {
					if(post_data.id === undefined) {
						self[post_data.index].id = response.data.id
					}
				})
				.catch(error => {
					alert(error)
				});
			loader.hide();
		},

		showModal(index) {
			this.planner.index = index;
			this.$refs['change-plan-modal'].show();
		},

		changePlan() {

			if (this.planner.value != null && this.planner.value != '') {

				// Set plans from to
				let start = this.planner.from,
					end = this.planner.to > this.month.daysInMonth ? this.month.daysInMonth : this.planner.to;

				for (let i = start; i <= end; i++) {
					this.items[this.planner.index][i].plan = Number(this.planner.value);
				}

				// POST
				this.reqSave({
					group_id: this.data.group_id,
					id: this.items[this.planner.index].id,
					name: this.items[this.planner.index].name,
					values: this.items[this.planner.index],
					index: this.planner.index
				});

				// Count total plan
				let total_plan = 0;

				for (let i = 1; i <= this.month.daysInMonth; i++) {
					total_plan += Number(this.items[this.planner.index][i].plan);
				}
				this.items[this.planner.index].total_plan = total_plan
				// Reset planner
				this.planner = {
					value: null,
					from: 1,
					to: this.month.daysInMonth,
					index: null,
				}
				this.$toast.info('План проставлен')
			}




			// Hide modal
			this.$refs['change-plan-modal'].hide();

		},

		deleteRecord(id, index) {
			if (!confirm('Вы уверены?')) {
				return '';
			}

			if(id != 0) {

				let url = '/timetracking/analytics/decomposition/delete';

				this.axios.delete(url, {
					headers: {},
					data: {
						id: id
					}
				})
					.then(() => {
						this.$toast.info('Пункт удален')
					})
					.catch(error => {
						alert(error)
					});
			}

			this.items.splice(index, 1);

		},

		calculateRecordsValues() {
			this.sum = {};
			this.itemsArray = [];

			this.records.forEach(item => {

				let cellValues = [],
					totalPlan = 0,
					totalFact = 0;


				for (let i = 1; i <= this.month.daysInMonth; i++) {

					cellValues[i] = item[i];

					if(item[i] === undefined) {
						cellValues[i] = {
							'plan': '',
							'fact': ''
						};
						continue;
					}
					if(item[i].plan !== undefined) {
						totalPlan += Number(item[i].plan)
					}

					if(item[i].fact !== undefined) {
						totalFact += Number(item[i].fact)
					}

				}

				this.itemsArray.push({
					name: item.name,
					id: item.id,
					editable: false,
					total_plan: totalPlan,
					total_fact: totalFact,
					group_id: this.data.group_id,
					...cellValues,
				});

			});
		},

		updateTable(items) {
			let loader = this.$loading.show();

			this.records = items;
			this.calculateRecordsValues();

			this.totalColumn()
			this.items = this.itemsArray;

			loader.hide();
		},

		addCellVariantsArrayToRecords(){
			this.itemsArray.forEach((element, key) => {
				this.itemsArray[key]['_cellVariants'] = [];
			});
		},

		totalColumn() {
			// let row0_avg = 0;
			// this.itemsArray.forEach((account, index) => {
			//     if(parseFloat(account['plan']) != 0 && account['plan'] != undefined) {
			//         row0_avg += parseFloat(account['plan']);
			//         console.log(account['plan'])
			//     }
			// })

			// this.itemsArray[0]['plan'] = row0_avg
		},

		editMode(item) {
			this.items.forEach(account => {
				account.editable = false
			})
			item.editable = true
		},

		toFloat(number) {
			return Number(number).toFixed(2);
		},
	},
};
</script>

<style lang="scss">
.TableDecomposition{
	&-inputName{
		&::placeholder{
			font-style: italic;
		}
	}
}

.bg-white {background:white}
.my-table.m2 tr .badge {
    opacity: 1;
}

    .day-minute {
        padding: 0 !important;
        text-align: center;
        vertical-align: middle;

        div {
            font-size: 0.8rem;
        }
        &.table-success {
            background-color: #01c601 !important;
        }

        &.table-danger {
            background-color: #fd1600 !important;
        }
    }

.table td, .table th, .table thead th{
    vertical-align: middle;
    min-width: 42px;
    text-align: center;
}
.table.b-table.table-sm>thead>tr>[aria-sort]:not(.b-table-sort-icon-left),
.table.b-table.table-sm>tfoot>tr>[aria-sort]:not(.b-table-sort-icon-left) {
    background-image: none !important;
    min-width: 32px;
}
.table .stat {
    background: #d9edff;
}
.table {
    position: relative;
}
.b-table-sticky-column{
    position: sticky;
    left: 0;
    z-index: 2;
}
.wd {
    font-size: 0.75rem;
    width: max-content;
    font-weight: 500;
}
.table .stat.plan{
    background: #007bff;
    color: #fff;
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
.w-250 {
    min-width: 250px;
}
.wd {
    .fa {
        opacity: 0;
    }
    &:hover {
        .fa {
            opacity: 1;
        }
    }
}

.strt tr:nth-child(2) th {
    background-color: #DDE9FF !important;
    text-align: center !important;
    border-top: 1px solid #b5c1d7 !important;
    border-left: 1px solid #b5c1d7 !important;
    border-bottom: 1px solid #b5c1d7 !important;
    border-right: 2px solid #b5c1d7 !important;
}

.strt{
    input{
        height: 30px;
        text-align: center;
        color: #333;
        width: 100px;
    }

	tbody {
		th, td {
			padding: 0 !important;

			&:first-child {
				padding: 0 10px !important;
			}

			& > div {
				height: 30px;
				width: 100px;
				display: inline-flex;
				align-items: center;
				justify-content: center;
			}

			&:first-child {
				& > div {
					width: auto;
				}

				input {
					text-align: left;
					width: 100%;
				}
			}
		}
	}
}

.day-minute div {
    font-size: 0.8rem;
    font-weight: 500;
}
.border-r-2 {
    border-right: 2px solid #b5c1d7 !important;
}
</style>
