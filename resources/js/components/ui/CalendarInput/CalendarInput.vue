<template>
	<div
		class="CalendarInput"
		v-scroll-lock="open && !popup"
	>
		<div
			v-if="!popup"
			class="CalendarInput-bg"
			@click.self.stop="$emit('close')"
		/>
		<div
			class="CalendarInput-content"
			:class="{'CalendarInput-content_popup': popup}"
			v-click-out="onClickOutside"
		>
			<CalendarInputBody />
			<CalendarInputNav
				v-if="hasTabs"
				:tabs="tabs"
			/>
		</div>
	</div>
</template>

<script>
import CalendarInputBody from './CalendarInputBody'
import CalendarInputNav from './CalendarInputNav'

export default {
	name: 'CalendarInput',
	components: {
		CalendarInputBody,
		CalendarInputNav,
	},
	directives: {
		'click-out': {
			bind(el, binding, vnode) {
				el.clickOutsideEvent = (event) => {
					if (!(el === event.target || el.contains(event.target))) {
						vnode.context[binding.expression](event);
					}
				};
				el.clickOutsideEventStop = (event) => { event.stopPropagation() }
				document.body.addEventListener('click', el.clickOutsideEvent);
				// el.addEventListener('click', el.clickOutsideEventStop)
			},
			unbind(el) {
				document.body.removeEventListener('click', el.clickOutsideEvent);
				// el.removeEventListener('click', el.clickOutsideEventStop)
			},
		}
	},
	provide(){
		return {
			getValue: () => this.value,
			getTSValue: () => this.tsValue,
			getFormat: () => this.format,
			getMonth: () => this.month,
			getYear: () => this.year,
			getRange: () => this.range,
			getSubmit: () => this.submit,
			getDaysInMonth: () => this.daysInMonth,
			getStartYear: () => this.startYear,
			getSeparateMonthYear: () => this.separateMonthYear,
			getOnlyMonth: () => this.onlyMonth,
			setValue: this.setValue,
			setMonth: this.setMonth,
			prevMonth: this.prevMonth,
			nextMonth: this.nextMonth,
			onTabToday: this.onTabToday,
			onTabTomorrow: this.onTabTomorrow,
			onTabCurrentMonth: this.onTabCurrentMonth,
			onTabPrevMonth: this.onTabPrevMonth,
			onTabCurrentYear: this.onTabCurrentYear,
			onTabPrevYear: this.onTabPrevYear,
			onTabAllTime: this.onTabAllTime,
			onTabCustom: this.onTabCustom,
			onSubmit: this.onSubmit,
		}
	},
	props: {
		open: {
			type: Boolean,
			default: false,
		},
		range: {
			type: Boolean,
			default: false,
		},
		submit: {
			type: Boolean,
			default: false,
		},
		value: {
			type: Array,
			default: () => ['']
		},
		tabs: {
			type: Array,
			default: () => [
				'Сегодня',
				'Завтра',
				'Текущий месяц',
				'Прошлый месяц',
				'Текущий год',
				'Прошлый год',
				'Все время',
			]
		},
		format: {
			type: String,
			default: 'DD.MM.YYYY'
		},
		popup: {
			type: Boolean,
			default: false
		},
		startYear: {
			type: Number,
			default: 2020
		},
		onlyMonth: {
			type: Boolean,
			default: false
		},
		separateMonthYear: {
			type: Boolean,
			default: false
		}
	},
	data(){
		const now = new Date()
		const data = {
			month: now.getMonth(),
			year: now.getFullYear(),
			currentDay: now.getDate(),
			currentMonth: now.getMonth(),
			currentYear: now.getFullYear(),
			tsValue: this.value.map(el => this.$moment(el, this.format).valueOf() || 0)
		}
		if(data.tsValue.length && data.tsValue[data.tsValue.length - 1]){
			const selected = new Date(data.tsValue[data.tsValue.length - 1])
			data.month = selected.getMonth()
			data.year = selected.getFullYear()
		}
		return data
	},
	computed: {
		daysInMonth(){
			return this.$moment([this.year, this.month]).daysInMonth()
		},
		hasTabs(){
			return this.tabs && this.tabs.length
		}
	},
	watch: {
		value(value){
			this.tsValue = value.map(el => this.$moment(el, this.format).valueOf() || 0)
		}
	},
	methods:{
		onClickOutside(e){
			this.$emit('close', e)
		},
		setValue(value){
			this.tsValue.push(value)
			this.tsValue.splice(0, this.tsValue.length - (this.range ? 2 : 1))
			// this.tsValue.sort((a, b) => a - b)
			if(!this.submit) this.$emit('input', this.tsValue.map(el => this.$moment(el).format(this.format)))
		},
		setMonth(month, year){
			// валидацию бы какую-нибудь
			this.month = month
			if(year) this.year = year
			if(this.onlyMonth){
				this.$emit('input', ['01.' + this.$moment([year, month]).format('MM.YYYY')])
				// this.$emit('close')
			}
		},
		prevMonth(){
			if(this.month - 1 < 0) {
				this.year--
				this.month = 11
				return
			}
			this.month--
		},
		nextMonth(){
			if(this.month + 1 > 11) {
				this.year++
				this.month = 0
				return
			}
			this.month++
		},
		onTabToday(){
			this.setMonth(this.currentMonth, this.currentYear)
			this.setValue(this.$moment([this.year, this.month, this.currentDay]).valueOf())
		},
		onTabTomorrow(){
			this.setMonth(this.currentMonth, this.currentYear)
			if(this.currentDay + 1 <= this.daysInMonth) return this.setValue(this.$moment([this.year, this.month, this.currentDay + 1]).valueOf())
			this.nextMonth()
			this.setValue(this.$moment([this.year, this.month, 1]).valueOf())
		},
		onTabCurrentMonth(){
			this.setMonth(this.currentMonth, this.currentYear)
			this.setValue(this.$moment([this.year, this.month, this.daysInMonth]).valueOf())
			this.setValue(this.$moment([this.year, this.month, 1]).valueOf())
		},
		onTabPrevMonth(){
			this.setMonth(this.currentMonth, this.currentYear)
			this.prevMonth()
			this.setValue(this.$moment([this.year, this.month, this.daysInMonth]).valueOf())
			this.setValue(this.$moment([this.year, this.month, 1]).valueOf())
		},
		onTabCurrentYear(){
			this.setMonth(11, this.currentYear)
			this.setValue(this.$moment([this.year, this.month, this.daysInMonth]).valueOf())
			this.setValue(this.$moment([this.year, 0, 1]).valueOf())
		},
		onTabPrevYear(){
			this.setMonth(11, this.currentYear - 1)
			this.setValue(this.$moment([this.year, this.month, this.daysInMonth]).valueOf())
			this.setValue(this.$moment([this.year, 0, 1]).valueOf())
		},
		onTabAllTime(){
			this.setMonth(this.currentMonth, this.currentYear)
			this.setValue(this.$moment([this.year, this.month, this.currentDay]).valueOf())
			this.setValue(0)
		},
		onTabCustom(tab){
			this.$emit('custom-tab', tab)
		},
		onSubmit(){
			this.$emit('input', this.tsValue.map(el => this.$moment(el).format(this.format)))
		}
	}
}
</script>

<style lang="scss">
.CalendarInput{
	&-bg{
		position: fixed;
		z-index: 100;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		background-color: rgba(#000, 0.25);
	}
	&-content{
		display: flex;
		flex-flow: row nowrap;
		align-items: stretch;
		border-radius: 1.6rem;
		position: absolute;
		top: 100%;
		right: 0;
		z-index: 101;
		background-color: #fff;
		&_popup{
			box-shadow: 0px 0px 3px rgba(0, 0, 0, 0.15), 0px 15px 60px -40px rgba(45, 50, 90, 0.2);
		}
	}
}
</style>
