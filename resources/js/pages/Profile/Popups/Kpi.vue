<template>
	<div
		class="popup__content mt-3"
		:class="{'v-loading': loading}"
	>
		<div class="popup__filter pb-4">
			<DateSelect
				v-model="selectedDate"
				class="ml-a Balance-datePicker"
			/>
		</div>

		<KpiContent
			:items="items"
			:groups="groups"
			:activities="activities"
			:fields="fields"
			:currency="currency"
		/>
	</div>
</template>

<script>
/* eslint-disable camelcase */

import { mapState, mapActions } from 'pinia'
import { usePortalStore } from '@/stores/Portal'
import { useProfileSalaryStore } from '@/stores/ProfileSalary'
import { kpi_fields, parseKPI, target2type /* , removeDeletedItems */ } from '../../kpi/kpis.js';
import { useYearOptions } from '@/composables/yearOptions'

import KpiContent from './KpiContent.vue'
import DateSelect from '../DateSelect'

export default {
	name: 'PopupKpi',
	components: {
		KpiContent,
		DateSelect,
	},
	props: {},
	data: function () {
		return {
			groups: [],
			editable: false,
			activities: [],
			items: [],
			dateInfo: {
				currentMonth: null,
				monthEnd: 0,
				workDays: 0,
				weekDays: 0,
				daysInMonth: 0
			},
			show_fields: [],
			all_fields: kpi_fields,
			fields: [],
			non_editable_fields: [
				'created_at',
				'updated_at',
				'created_by',
				'updated_by',
			],
			user_id: 1,
			loading: false,
			selectedDate: this.$moment().format('DD.MM.YYYY'),
			currency: 'KZT',
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
				this.fetchBefore()
			})
		}
	},
	created(){
		this.setMonth()
		this.prepareFields()
		this.fetchBefore()
	},
	methods: {
		...mapActions(useProfileSalaryStore, ['setReadedKpis']),
		/**
		 * set month
		 */
		setMonth() {
			let year = this.$moment().format('YYYY')
			this.dateInfo.currentMonth = this.dateInfo.currentMonth ? this.dateInfo.currentMonth : this.$moment().format('MMMM')
			// this.currentMonth = this.dateInfo.currentMonth;
			this.dateInfo.date = `${this.dateInfo.currentMonth} ${year}`

			let currentMonth = this.$moment(this.dateInfo.currentMonth, 'MMMM')

			//Расчет выходных дней
			this.dateInfo.monthEnd = currentMonth.endOf('month'); //Конец месяца
			this.dateInfo.weekDays = currentMonth.weekdayCalc(this.dateInfo.monthEnd, [6]) //Колличество выходных
			this.dateInfo.daysInMonth = currentMonth.daysInMonth() //Колличество дней в месяце
			this.dateInfo.workDays = this.dateInfo.daysInMonth - this.dateInfo.weekDays //Колличество рабочих дней
		},

		fetchBefore() {
			this.fetchData({
				data_from: {
					year: this.currentYear,
					month: this.$moment(this.currentMonth, 'MMMM').format('M')
				},
				user_id: this.$laravel.userId
			})
		},

		fetchData(filters = null) {
			this.loading = true

			this.axios.post('/statistics/kpi-with-currency', {
				filters: filters
			}).then(({data}) => {
				if(!Array.isArray(data.items)) data.items = Object.values(data.items || {})
				// items
				this.items = data.items.map(res=> {
					const kpi = parseKPI(res)
					kpi.completed_80 = kpi.completed_80 * data.currency_rate
					kpi.completed_100 = kpi.completed_100 * data.currency_rate
					return {...kpi, my_sum: 0}
				}).sort((a, b) => target2type[a.targetable_type] - target2type[b.targetable_type])
				// removeDeletedItems(this.items)

				this.activities = data.activities;
				this.groups = data.groups;
				this.currency = data.currency

				this.setReadedKpis()

				this.loading = false
			}).catch(error => {
				this.loading = false
				alert(error)
			});
		},

		prepareFields() {
			let visible_fields = [] // ??????

			kpi_fields.forEach(field => {
				if(this.show_fields[field.key]) {
					visible_fields.push(field)
				}
			});

			this.fields = kpi_fields;
		},
	}
};
</script>
