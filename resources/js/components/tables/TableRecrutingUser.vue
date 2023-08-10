<template>
	<div
		v-if="!deleted"
		:class="{'basic': id != 0}"
	>
		<div class="d-flex justify-content-between">
			<div class="mb-2 r-name">
				<b>{{ name }}</b>
			</div>
			<div>
				<button
					v-if="editable && id != 0"
					class="btn btn-primary btn-sm rounded mb-2"
					@click="deleteTable"
				>
					<span>Удалить</span>
				</button>
				<button
					v-if="editable"
					class="btn btn-primary btn-sm rounded mb-2"
					@click="toggleVisible"
				>
					<span v-if="visible">Скрыть</span>
					<span v-else>Показать</span>
				</button>
			</div>
		</div>

		<div v-if="visible">
			<b-table
				responsive
				striped
				class="text-nowrap text-right my-table my-tabl-max mb-3 recruting-user"
				:small="true"
				:bordered="true"
				:items="records"
				:fields="fields"
				primary-key="a"
			>
				<template #cell()="data">
					<div
						v-if="data.index == 7 && editable"
						:class="{
							'plus' : Number(data.value) >= Number(data.item.plan) && Number(data.value ) != 0,
							'minus' : Number(data.value) < Number(data.item.plan) && Number(data.value ) != 0,
						}"
					>
						<input
							v-model="data.value"
							type="number"
							min="0"
							class="form-control cell-input"
							@change="updateSettings($event,data)"
						>
					</div>

					<div
						v-else
						:class="{
							'plus' : (data.index == 0 || data.index == 1 || data.index == 7) && Number(data.value ) != 0 && Number(data.value) >= Number(data.item.plan),
							'minus' : (data.index == 0 || data.index == 1 || data.index == 7) && Number(data.value ) != 0 && Number(data.value) < Number(data.item.plan),
						}"
					>
						{{ data.value }}
					</div>
				</template>

				<template #cell(plan)="data">
					<input
						v-if="(data.index == 0 || data.index == 1 || data.index == 6 || data.index == 7 || data.index == 8) && editable"
						v-model="data.value"
						type="number"
						min="0"
						class="form-control cell-input"
						@change="updateSettings($event,data)"
					>
					<div v-else>
						{{ data.value }}
					</div>
				</template>

				<template #cell(month_plan)="data">
					<div v-if="[0,1,6,7].includes(data.index)">
						{{ data.item.plan * workdays }}
					</div>
				</template>

				<template #cell(headers)="data">
					<div>{{ data.value }}</div>
				</template>

				<template #cell(fact)="data">
					<div>{{ data.value }}</div>
				</template>

				<template #cell(conversion)="data">
					<div>{{ data.value }}</div>
				</template>
			</b-table>
		</div>
	</div>
</template>

<script>
/* eslint-disable vue/no-mutating-props */
export default {
	name: 'TableRecrutingUser',
	props: {
		name: String,
		id: Number,
		records: Array,
		workdays: {
			type: Number,
			default: 27
		},
		month: Object,
		editable: {
			type: Boolean,
			default: false
		},
		deleted: {
			type: Boolean,
			default: false
		},
		hr: {
			type: Object,
			default: () => ({})
		}
	},
	data: function () {
		return {
			fields: [],
			hasPremission: false,
			visible: true,
		};
	},
	watch: {
		// эта функция запускается при любом изменении данных
		records: {
			// the callback will be called immediately after the start of the observation
			immediate: true,
			handler () {
				this.setFields()
				for (let index = 0; index < 8; index++) {
					this.calcTotal(index)
				}
				this.calcConversionAuto()
			}
		},
	},

	mounted() {
		this.setFields()
		for (let index = 0; index < 8; index++) {
			this.calcTotal(index)
		}
		this.calcConversionAuto()



		if(localStorage['recruiter_' + this.id + '_deleted']) {
			this.hr.deleted = true;
		} else if(localStorage['recruiter_' + this.id]) {
			this.visible = JSON.parse(localStorage.getItem('recruiter_' + this.id));
		}

	},

	methods: {
		toggleVisible() {
			this.visible = !this.visible
			localStorage['recruiter_' + this.id] = this.visible;
		},

		deleteTable() {
			this.deleted = true;
			localStorage['recruiter_' + this.id + '_deleted'] = true;
		},

		setFields() {
			let fields = [];

			fields = [
				{
					key: 'headers',
					label: this.name,
					variant: 'title',
					class: 'text-left t-name b-table-sticky-column bgw'
				},
				{
					key: 'conversion',
					label: '%',
				},
				{
					key: 'month_plan',
					label: 'План',
				},
				{
					key: 'plan',
					label: 'В день',
				},
				{
					key: 'fact',
					label: 'Факт',
				}
			];

			for (let i = 1; i <= this.month.daysInMonth; i++) {
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

			this.updateNumber(e, data);



			let loader = this.$loading.show();

			this.axios.post('/timetracking/update-settings-individually', {
				date: this.$moment(
					`${this.month.currentMonth} ${new Date().getFullYear()}`,
					'MMMM YYYY'
				).format('YYYY-MM-DD'),
				group_id: 48,
				table_type: 0,
				day: data.field.key,
				employee_id: this.id,
				settings: this.records, // data of employee for 1 month
			})
				.then(() => {
					loader.hide();
				});

		},

		updateNumber(e, data) {


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
			this.records[index].fact = sum

			if(index == 1 || index == 6 || index == 7 || index == 8) {
				this.calcConversion(index)
			}
		},

		calcConversion() {
			this.calcConversionAuto()

		},

		calcConversionAuto() {
			if(this.records[1] !== undefined && this.records[6] !== undefined && this.records[0] !== undefined) {
				this.records[1].conversion =  Number(this.records[1].fact / this.records[0].fact * 100).toFixed(1)
				if (isNaN(this.records[1].conversion)) this.records[1].conversion = 0
				this.records[1].conversion = this.records[1].conversion + '%'

				this.records[6].conversion =  Number(this.records[6].fact / this.records[1].fact * 100).toFixed(1)
				if (isNaN(this.records[6].conversion)) this.records[6].conversion = 0
				this.records[6].conversion = this.records[6].conversion + '%'
			}

		}

	}
};
</script>

<style lang="scss" scoped>
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

.my-table-max.recruting-user tr:nth-child(7),
.my-table-max.recruting-user tr:nth-child(8),
.my-table-max.recruting-user tr:nth-child(9) {
        td:first-child {
            div {
                text-align: right;
            }

            font-style: italic;
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


.my-table-max .day.Sat, .my-table-max .day.Sun {
    background-color: #cedaeb;
    border-color: #dfecfe;
}

input[type="time"]::-webkit-calendar-picker-indicator {
    background: none;
    display:none;
}

.basic .plus {
    background: rgb(162, 255, 172);
}
.basic .minus {
    background: rgb(255, 179, 179);
}
.r-name {
    color: #1076b0;
    font-size: 15px;
    border-bottom: 1px solid #1076b0;
}
</style>
