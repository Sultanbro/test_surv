<template>
<div class="popup__content mt-3" :class="{'v-loading': loading}">
    <div class="popup__filter">
        <select class="select-css" v-model="currentMonth" @change="fetchBefore()">
            <option
                v-for="month in $moment.months()"
                :value="month"
                :key="month"
            >
                {{ month }}
            </option>
        </select>
    </div>
    <div class="popup__award">

        <template v-if="items.length">
            <template v-for="(item, i) in items">
                <div class="award__title popup__content-title" :key="i">
                    За период с {{ new Date(item.items.from).toLocaleDateString('RU') }} до {{ new Date(item.items.to).toLocaleDateString('RU') }}
                </div>
                <table class="award__table" :key="i">
                    <tr>
                        <td class="blue">Сумма премии</td>
                        <td>{{ item.items.sum }}</td>
                    </tr>
                    <tr v-if="item.items.activity">
                        <td class="blue">План</td>
                        <td>
                            <div>
                                <b>Активность: {{ item.items.activity.name }}</b>
                            </div>
                            <div>{{ item.items.plan }}</div>
                        </td>
                    </tr>
                    <tr>
                        <td class="blue">Условия</td>
                        <td>{{ item.items.text }}</td>
                    </tr>
                </table>
            </template>
        </template>

        <p v-else>Обратитесь к своему руководителю, если хотите чтобы вам была назначена квартальная премия</p>
    </div>
</div>
</template>

<script>
export default {
	name: 'PopupQuartal',
	props: {},
	data: function () {
		return {
			items: [],
			activities: [],
			groups: [],
			currentMonth: null,
			dateInfo: {
				currentMonth: null,
				monthEnd: 0,
				workDays: 0,
				weekDays: 0,
				daysInMonth: 0
			},
			loading: false
		};
	},
	created(){
		this.setMonth()
		this.fetchBefore()
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

		fetchBefore() {
			this.fetchData({
				data_from: {
					year: new Date().getFullYear(),
					month: this.$moment(this.currentMonth, 'MMMM').format('M')
				},
				user_id: this.$laravel.userId
			})
		},

		fetchData(filters = null) {
			this.loading = true

			this.axios.post('/statistics/quartal-premiums', {
				filters: filters
			}).then(response => {

				this.items = response.data[0];

				// this.defineSourcesAndGroups('t');

				this.items.forEach(el => el.expanded = true);

				this.loading = false
			}).catch(error => {
				this.loading = false
				alert(error)
			});
		},

		defineSourcesAndGroups() {
			this.items.forEach(p => {
				p.items.forEach(el => {
					el.source = 0;
					el.group_id = 0;

					if(el.activity_id != 0) {
						let i = this.activities.findIndex(a => a.id == el.activity_id);
						if(i != -1) {
							el.source = this.activities[i].source
							if(el.source == 1) el.group_id = this.activities[i].group_id
						}
					}
				});
			})
		},
	}
};
</script>