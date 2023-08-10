<template>
	<div class="mt-5">
		<b-tabs content-class="mt-3">
			<template v-for="activity in activitiess"> 
				<b-tab
					v-if="![19,21].includes(activity.id)"
					:key="activity.id"
					card
					:title="activity.name"
				>
					<t-activity-collection
						v-if="activity.type == 'collection'"
						:key="activity.id"
						:month="monthInfo"
						:activity="activity"
						:is_admin="false"
						:price="activity.price"
					/>

					<t-activity-new
						v-else-if="activity.type == 'default'"
						:key="activity.id"
						:month="monthInfo"
						:activity="activity"
						:group_id="activity.group_id"
						:work_days="activity.workdays"
						:editable="false"
						:show_headers="false"
					/>
				</b-tab>
			</template>
        
			<b-tab title="Контроль Качества">
				<t-quality-weekly 
					:month-info="monthInfo"
					:items="quality"
				/>
			</b-tab>
		</b-tabs>
	</div>
</template>

<script>
export default {
	name: 'TableUserAnalytics', 
	props: ['activities', 'quality'],
	data() {
		return {
			activitiess: [],
			currentYear: new Date().getFullYear(),
			monthInfo: {
				currentMonth: null,
				monthEnd: 0,
				workDays: 0,
				weekDays: 0,
				workDays5: 0,
				weekDays5: 0,
				daysInMonth: 0,
				year: new Date().getFullYear()
			},
		}
	},
	created() {
		this.setMonthInfo();
		this.activitiess = JSON.parse(this.activities)
	},
	methods: {

		setMonthInfo() {
			this.monthInfo.currentMonth = this.monthInfo.currentMonth ? this.monthInfo.currentMonth : this.$moment().format('MMMM')
			this.monthInfo.month = this.monthInfo.currentMonth ? this.$moment(this.monthInfo.currentMonth, 'MMMM').format('M') : this.$moment().format('M')
			let currentMonth = this.$moment(this.monthInfo.currentMonth, 'MMMM')
			//Расчет выходных дней
			this.monthInfo.monthEnd = currentMonth.endOf('month'); //Конец месяца
			this.monthInfo.weekDays = currentMonth.weekdayCalc(currentMonth.startOf('month').toString(), currentMonth.endOf('month').toString(), [6]) //Колличество выходных
			this.monthInfo.weekDays5 = currentMonth.weekdayCalc(currentMonth.startOf('month').toString(), currentMonth.endOf('month').toString(), [6,0]) //Колличество выходных
			this.monthInfo.daysInMonth = new Date(this.$moment().format('YYYY'), this.$moment(this.monthInfo.currentMonth, 'MMMM').format('M'), 0).getDate() //Колличество дней в месяце
			this.monthInfo.workDays = this.monthInfo.daysInMonth - this.monthInfo.weekDays //Колличество рабочих дней
			this.monthInfo.workDays5 = this.monthInfo.daysInMonth - this.monthInfo.weekDays5 //Колличество рабочих дней
            
			this.currentYear = this.$moment().format('YYYY') //Установка выбранного года 
			this.monthInfo.currentYear = this.currentYear;
		},    

	} 
}
</script>

<style>

</style> 
