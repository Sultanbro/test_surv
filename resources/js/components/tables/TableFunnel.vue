<template>
	<div>
		<div class="row align-items-center">
			<!-- <div class="col-3">
            <div :id="'funnel-' + id" class="funnel"></div>
        </div> -->
			<div class="col-12">
				<JobtronTable
					:key="tableKey"
					responsive
					striped
					class="text-nowrap mb-3 table-funnel"
					:small="true"
					:bordered="true"
					:items="items"
					:fields="fields"
					primary-key="a"
				>
					<template #header(name)="data">
						<input
							:ref="'mylink' + segment"
							type="text"
							class="hider"
						>
						<span>{{ data.field.label }}</span>


						<i
							class="fa fa-clone ffpointer"
							@click="copy()"
						/>
					</template>

					<template #cell(name)="data">
						<div>
							{{ data.value }}
						</div>
					</template>

					<template #cell="data">
						<div
							v-if="data.item.show == 1 && type == 'week'"
							class="TableFunnel-input"
						>
							<input
								v-model="data.value"
								type="number"
								class="form-control form-control-sm"
								@change="updateSettings($event,data)"
							>
						</div>
						<div v-else>
							{{ data.value }}
						</div>
					</template>
				</JobtronTable>
			</div>
		</div>
	</div>
</template>

<script>
import JobtronTable from '@ui/Table'
// import D3Funnel from 'd3-funnel';

export default {
	name: 'TableFunnel',
	components: {
		JobtronTable,
	},
	props: {
		table: Array,
		title: String,
		type: String,
		segment: String,
		date: String,
		id: Number,
		// rates: Array,
		// daysInMonth: {
		//     default: new Date().getDate(),
		//     type: Number,
		// }
	},
	data: function () {
		return {
			items: [],
			days: [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31],
			chart : null,
			keys: [],
			tableKey: 1,
			fields: [],
			chartOptions: {
				options: {
					block: {
						dynamicHeight: true,
						minHeight: 60,
						fill: {
							type: 'gradient' // gradient
						},
						highlight: true
					},
					chart: {
						curve: {
							enabled: false
						},
						animate: 100,
						bottomPinch: 0
					},
					tooltip: {
						enabled : true,
						format: function(label) {
							if(label == 'Создано новых лидов за день') return 'Лиды с названиями: Удаленный, inhouse'
							if(label == 'Сконвертировано') return 'Создано сделок на основе лидов'
							if(label == 'Стажируются') return 'Количество стажеров присутствовавших на сегодняшнем обучении'
							if(label == 'Приняты в BP') return 'Приняты сотрудниками'

						}
					},
					label: {
						fontFamily: 'Open Sans',
						fontSize: '12px',
						format: '{l}\n{f}'
					},
					events :{
						click: {
							block: function(/* data */) {

							}
						}
					}
				},
				data: [],
			},
		};
	},
	watch: {
		date: function() { // watch it
			this.tableKey++;
			this.setFields()
			this.items = this.table
			this.fields[0].label = this.title
			this.calc()
		},
		table: {
			handler: function() {
				this.calc()
			},
			deep: true
		},
	},
	created() {
		this.setFields()
		this.items = this.table
		this.fields[0].label = this.title
		this.calc()
	},
	methods: {

		copy() {

		},

		setFields() {

			this.fields = [
				{
					key: 'name',
					label: 'Показатели',
					variant: 'title',
					class: 'text-left rownumber JobtronTable-sticky'
				},
			];

			if(this.type == 'month') {
				let months = {
					1: 'Январь',
					2: 'Февраль',
					3: 'Март',
					4: 'Апрель',
					5: 'Май',
					6: 'Июнь',
					7: 'Июль',
					8: 'Август',
					9: 'Сентябрь',
					10: 'Октябрь',
					11: 'Ноябрь',
					12: 'Декабрь',
				};

				//moment("11-26-2016", "MMDDYYYY").week();

				Object.keys(months).forEach(key => {
					this.fields.push({
						key: `${key}`,
						label: months[key],
						class: 'day'
					});

					this.keys.push(key);
				})


			}

			if(this.type == 'week') {
				let date = this.$moment(this.date, 'YYYY-MM-DD'),
					daysInMonth = date.daysInMonth(),
					isoWeek = date.isoWeek(),
					weekNumber = 1;

				let firstDayOfWeek = date.format('DD.MM');



				for(let i = 1; i <= daysInMonth; i++) {


					if(date.isoWeek() != isoWeek) {


						this.fields.push({
							key: `${weekNumber}`,
							label: firstDayOfWeek + ' - ' + date.subtract(1, 'days').format('DD.MM'),
							class: 'day'
						});

						this.keys.push(weekNumber);
						firstDayOfWeek = date.add(1, 'days').format('DD.MM')

						isoWeek = date.isoWeek();
						weekNumber++;

					}

					if(i == daysInMonth) {
						this.fields.push({
							key: `${weekNumber}`,
							label: firstDayOfWeek + ' - ' + date.format('DD.MM'),
							class: 'day'
						});
						this.keys.push(weekNumber);
					}

					date.add(1, 'days')
				}

				// console.log(date.add(1, 'days').format('YYYY-MM-DD'));


			}


		},

		updateSettings(e, data) {
			if(this.type == 'month') return '';
			this.items[data.index][data.field.key] = data.value;
			this.updateTable(data.field.key)

			this.axios.post('/timetracking/update-settings-extra', {
				date: this.date,
				group_id: 48,
				settings: this.items,
				type: this.segment
			}).then(() => {
				this.$toast.success('Сохранено!');
			}).catch(() => {
				this.$toast.error('Ошибка');
			})

		},

		calc() {
			for(let i = 0; i < this.keys.length; i++) {
				//  console.log(this.keys)
				// console.log(this.keys[i])
				this.updateTable(this.keys[i])
			}
		},

		updateTable(key) {
			if(this.segment == 'hh') {
				this.items[6][key] = this.percentage(this.items[2][key], this.items[1][key]);// Конверсия 1
				this.items[7][key] = this.percentage(this.items[8][key], this.items[2][key]);// Конверсия 2
				this.items[5][key] = this.divide(this.items[0][key], this.items[1][key]); // CPL Затраты / Создано лидов

				this.items[9][key] = this.divide(this.items[0][key], this.items[8][key]); // CAC (Стоимость привлечения оператора) Затраты / Принято
			}

			if(this.segment == 'segments') {
				this.items[10][key] = this.percentage(this.items[5][key], this.items[4][key]);// Конверсия 1
				this.items[11][key] = this.percentage(this.items[12][key], this.items[5][key]);// Конверсия 2
				this.items[8][key] = this.divide(this.items[0][key], this.items[3][key]); // CPC (Стоимость клика) Затраты / Переходы
				this.items[9][key] = this.divide(this.items[0][key], this.items[4][key]); // CPL Затраты / Создано лидов
				this.items[13][key] = this.divide(this.items[0][key], this.items[12][key]); // CAC (Стоимость привлечения оператора) Затраты / Принято
			}

			if(this.segment == 'insta') {
				this.items[10][key] = this.percentage(this.items[5][key], this.items[4][key]);// Конверсия 1
				this.items[11][key] = this.percentage(this.items[12][key], this.items[5][key]);// Конверсия 2
				this.items[9][key] = this.divide(this.items[0][key], this.items[4][key]); // CPL Затраты / Создано лидов
				this.items[13][key] = this.divide(this.items[0][key], this.items[12][key]); // CAC (Стоимость привлечения оператора) Затраты / Принято
				this.items[8][key] = this.divide(this.items[0][key], this.items[3][key]); // CPC (Стоимость клика) Затраты / Переходы
			}

			if(this.segment == 'alina' || this.segment == 'saltanat' || this.segment == 'akzhol' || this.segment == 'darkhan') {
				this.items[4][key] = this.percentage(this.items[5][key], this.items[0][key]); // Конверсия
			}
		},

		percentage(a, b) {
			let res = Number(a) / Number(b) * 100;
			if(isNaN(res)) {
				res = '0 %';
			} else {
				res = Number(res).toFixed(2) + ' %';
			}
			return res;
		},

		divide(a, b) {
			let res = Number(a) / Number(b);
			if(isNaN(res)) res = 0;
			return Number(res).toFixed(2);
		},

	}
};
</script>
<style lang="scss">
.TableFunnel{
	&-input{
		margin: -12px -15px;
	}
}
.funnel {
	max-width: 100%;
	width: 330px;
	display: block;
	margin: 0 auto;
	height: 330px;
}
.table-funnel {
	max-width: 960px;
	.JobtronTable-sticky {
		width: 310px;
		font-weight: 700;
	}
	// table {
	//     max-width: 100px;
	// }
	td:first-child {
		background: #67F1C8;
	}
	.day {
		text-align: center;
	}
	.form-control {
		height: auto !important;
		padding: 12px 15px !important;

		border: 0px solid transparent;
		border-radius: 0 !important;
		text-align: center;
	}
}
.hider {
	position: absolute;
	left: -10px;
	width: 10px;
	height: 10px;
	opacity: 0;
	display: block;
}
.ff.pointer {
	margin-top: 2px;
	cursor: pointer;
}
</style>
