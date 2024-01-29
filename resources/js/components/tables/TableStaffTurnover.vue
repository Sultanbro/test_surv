<template>
	<div>
		<div class="staff-turnover">
			<div class="row mb-2">
				<div class="col-12 d-flex align-items-center mb-5">
					<div class="table-responsive">
						<JobtronTable
							:fields="fields"
							:items="staff"
						/>
					</div>
				</div>

				<div class="col-12 align-items-center mb-5 mt-5">
					<h4 class="mb-5 text-center">
						Текучка по отделам
					</h4>
					<div class="table-responsive">
						<JobtronTable
							:fields="fields"
							:items="staff_by_group"
						/>
					</div>
				</div>

				<div class="col-12 align-items-center mb-5 mt-5">
					<h4 class="mb-5 text-center">
						Продолжительность работы до 1 мес/ до 3 мес/ более 3 мес
					</h4>
					<div class="table-responsive">
						<JobtronTable
							:fields="fields"
							:items="staff_longevity"
						/>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
/* eslint-disable camelcase */
/* eslint-disable vue/prop-name-casing */

import JobtronTable from '@ui/Table'

export default {
	name: 'TableStaffTurnover', // Раньше был нужен чтобы собирать скайпы, сейчас собираются стажеры для Zoom обучения
	components: {
		JobtronTable,
	},
	props: {
		staff: {
			type: Array,
			default: () => [],
		},
		staff_by_group: {
			type: Array,
			default: () => [],
		},
		staff_longevity: {
			type: Array,
			default: () => [],
		},
		causes: {
			type: Array,
			default: () => [],
		},
	},
	data: function () {
		const headerClass = 'text-left first-width'
		const cellClass = 'text-center'
		const months = [
			'Январь',
			'Февраль',
			'Март',
			'Апрель',
			'Май',
			'Июнь',
			'Июль',
			'Август',
			'Сентябрь',
			'Октябрь',
			'Ноябрь',
			'Декабрь',
		]
		const fields = [
			{
				key: 'name',
				label: '',
				thClass: headerClass,
				tdClass: headerClass,
			}
		]
		for(let i = 1; i < 13; ++i){
			fields.push({
				key: 'm' + i,
				label: months[i - 1],
				thClass: cellClass,
				tdClass: cellClass
			})
		}
		return {
			fields, // поля таблицы
		};
	},
	watch: {
		// month: {
		//     // the callback will be called immediately after the start of the observation
		//     deep: true,
		//     handler (val, oldVal) {
		//         this.filterTable()
		//     }
		// },
	},

	mounted() {

	},

	methods: {


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

.cell-input {
    background: none;
    border: none;
    text-align: center;
    appearance: textfield;
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
.my-table td div {
    position: relative !important;
    padding: 0;
    // user-select: none;
    // width: min-content;
    // white-space: pre-line;
}

.my-table-max .day.Sat, .my-table-max .day.Sun {
    background-color: #cedaeb;
    border-color: #dfecfe;
}

input[type="time"]::-webkit-calendar-picker-indicator {
    background: none;
    display:none;
}

</style>
