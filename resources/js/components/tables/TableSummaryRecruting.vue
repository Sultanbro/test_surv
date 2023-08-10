<template>
	<div>
		<b-table
			responsive
			striped
			class="text-nowrap text-right my-table my-table-max mb-3 summary-recruiting"
			:small="true"
			:bordered="true"
			:items="items"
			:fields="fields"
			primary-key="a"
		>
			<template #cell()="data">
				<div v-if="data.index == S_EMPTY7 || data.index == S_EMPTY8 || data.index == S_EMPTY11" />
				<div v-else>
					<input
						v-if="data.field.key == 'plan' && (data.index != S_APPLIED || data.index != S_FIRED)"
						type="number"
						class="form-control cell-input"
						:value="data.value"
						@change="updateSettings($event,data)"
					>
					<div v-else>
						{{ data.value }}
					</div>
				</div>
			</template>
		</b-table>
	</div>
</template>

<script>
/* eslint-disable vue/no-mutating-props */
// const S_CREATED = 0; // Создано новых лидов за день
// const S_CALLS_OUT = 1; // ИСХ успешные соединения
// const S_CALLS_OUT_10 = 2; // Успешные соединения от 10 сек
// const S_CALLS_IN = 3; // ВХ успешные соединения
// const S_CALLS_MISSED = 4; // Пропущенные звонки
// const S_FAILED = 5; // Забраковано Лидов
const S_PROCESSED = 6; // Обработанные лиды FAILED + CONVERTED
// const S_ONLINE = 7; // Количество рекрутеров на линии
// const S_CONVERTED_CONVERSION = 8; // Конверсия сконвертированных от обработанных
const S_CONVERTED = 9; // Сконвертировано Лидов
/* const S_EMPTY7 = 11;
const S_EMPTY8 = 12;
const S_TRAINING_TODAY = 13; // Стажируются сегодня
const S_EMPTY11 = 14; */
const S_APPLIED = 15; // Приняты на работу
// const S_FIRED = 16; // Уволены

export default {
	name: 'TableSummaryRecruting',
	props: {
		records: Array,
		month: Object,
	},
	data: function () {
		return {
			items: [],
			fields: [],
			workDays: 26,
			hasPremission: false,
		};
	},
	watch: {
		// эта функция запускается при любом изменении данных
		records: {
			// the callback will be called immediately after the start of the observation
			immediate: true,
			handler (val) {
				this.setFields()
				this.items = val
				this.calcConversionAuto()
			}
		},
	},

	mounted() {
		this.setFields()
		this.calcConversionAuto()
	},

	created() {
		this.S_CREATED = 0; // Создано новых лидов за день
		this.S_CALLS_OUT = 1; // ИСХ успешные соединения
		this.S_CALLS_OUT_10 = 2; // Успешные соединения от 10 сек
		this.S_CALLS_IN = 3; // ВХ успешные соединения
		this.S_CALLS_MISSED = 4; // Пропущенные звонки
		this.S_FAILED = 5; // Забраковано Лидов
		this.S_PROCESSED = 6; // Обработанные лиды FAILED + CONVERTED
		this.S_ONLINE = 7; // Количество рекрутеров на линии
		this.S_CONVERTED_CONVERSION = 8; // Конверсия сконвертированных от обработанных
		this.S_CONVERTED = 9; // Сконвертировано Лидов
		this.S_EMPTY7 = 11;
		this.S_EMPTY8 = 12;
		this.S_TRAINING_TODAY = 13; // Стажируются сегодня
		this.S_EMPTY11 = 14;
		this.S_APPLIED = 15; // Приняты на работу
		this.S_FIRED = 16; // Уволены
	},

	methods: {


		setFields() {
			let fields = [];

			fields = [
				{
					key: 'headers',
					label: 'Рекрутинг',
					variant: 'title',
					class: 'text-left t-name b-table-sticky-column bgz'
				},
				{
					key: 'conversion',
					label: 'Конверсия, %',
				},
				{
					key: 'plan',
					label: 'План',
				},
				{
					key: 'fact',
					label: 'Факт',
				}
			];

			for(let i = 1; i <= this.month.daysInMonth; i++) {
				let dayName = this.$moment(`${i} ${this.month.currentMonth} ${new Date().getFullYear()}`, 'D MMMM YYYY').locale('en').format('ddd')
				fields.push({
					key: `${i}`,
					label: `${i}`,
					class: ` day  ${dayName}`,
					is_date: true
				});
			}

			this.fields = fields;
		},

		updateSettings(e, data) {

			this.updateTable(e, data);

			let loader = this.$loading.show();

			this.axios.post('/timetracking/update-settings', {
				date: this.$moment(`${this.month.currentMonth} ${new Date().getFullYear()}`, 'MMMM YYYY').format('YYYY-MM-DD'),
				group_id: 48,
				settings: this.records
			}).then(() => {
				loader.hide()
			})

		},

		updateTable(e, data) {
			var index = data.index
			var clearedValue = e.target.value.replace(',', '.');
			var value = parseFloat(clearedValue) || null
			var key = data.field.key
			this.records[index][key] = value

			this.calcTotal(index)
		},

		calcTotal(index) {

			let sum = 0;
			for(let i = 1; i <= this.month.daysInMonth; i++) {
				if (isNaN(this.records[index][i])) continue;

				sum += Number(this.records[index][i]);
			}
			this.records[index].fact = Number(sum)



			if(index == S_CONVERTED || index == S_APPLIED) {
				this.calcConversion(index)
			}
		},

		calcConversion(index) {

			let array = {
				converted: this.records[S_CONVERTED].fact,
				processed: this.records[S_PROCESSED].fact,
				applied: this.records[S_APPLIED].fact,
			}

			if(index == S_CONVERTED) {
				this.records[index].conversion =  Number(array.converted / array.processed * 100).toFixed(1)
			}

			if(index == S_APPLIED) {
				this.records[index].conversion =  Number(array.applied / array.converted * 100).toFixed(1)
			}

			if (isNaN(this.records[index].conversion)) {
				this.records[index].conversion = '0%'
			}

			this.calcConversionAuto()
		},


		calcConversionAuto() {
			this.records[S_CONVERTED].conversion =  Number(this.records[S_CONVERTED].fact / this.records[S_PROCESSED].fact * 100).toFixed(1)
			if (isNaN(this.records[S_CONVERTED].conversion)) this.records[S_CONVERTED].conversion = 0
			this.records[S_CONVERTED].conversion = this.records[S_CONVERTED].conversion + '%'

			this.records[S_APPLIED].conversion =  Number(this.records[S_APPLIED].fact / this.records[S_APPLIED].fact * 100).toFixed(1)
			if (isNaN(this.records[S_APPLIED].conversion)) this.records[S_APPLIED].conversion = 0
			this.records[S_APPLIED].conversion = this.records[S_APPLIED].conversion + '%'
		}

	}
};
</script>

<style lang="scss">
.my-table-max {
    max-height: inherit !important;

    .day {
        padding: 0 !important;
        text-align: center;

        &.table-success {
            background-color: #29dc29 !important;
        }

        &.table-danger {
            background-color: #e4585f !important;
        }

        &.Sat,
        &.Sun {
            background-color: #FEF2CB;
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

.summary-recruiting {
    &.my-table {
        .day {
            div {
                font-weight: 500;
                color: #161616;
            }
        }
        td {
            .t-name {
                div {
                    font-weight: 700;
                }
            }
        }
    }

    td {

        input {
            min-width: 50px;
        }
        &:nth-child(4) {
           background: #e9eff6;
            font-weight: 700;
            color: #000;
        }
    }
    th {
        background: #2dad4a !important;
    }
    .table-striped tbody  {

        tr:nth-child(6),
        tr:nth-child(9),
        tr:nth-child(13) {
            background: #e9eff6;
        }

    }
}
.summary-recruiting
.bgz {
    background: #e1f3e5;
}
</style>
