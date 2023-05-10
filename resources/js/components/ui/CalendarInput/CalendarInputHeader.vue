<template>
	<div class="CalendarInputHeader">
		<div class="CalendarInputHeader-title">
			<template v-if="separateMonthYear">
				<span class="CalendarInputHeader-text">
					<span
						class="CalendarInputHeader-year"
						@click.stop="selectYear"
					>{{ year }}</span>
					<span
						class="CalendarInputHeader-month"
						@click.stop="selectMonth"
					>
						{{ monthName | capitalize }}
					</span>
				</span>
				<PopupMenu
					v-show="isPopupYear"
					:max-height="'30vh'"
					position="bottomLeft"
					v-click-out="togglePopup"
				>
					<div
						v-for="opt, i in yearOptions"
						:key="i"
						class="PopupMenu-item"
						@click="onSelectYear(opt)"
					>
						{{ opt }}
					</div>
				</PopupMenu>
				<PopupMenu
					v-show="isPopupMonth"
					:max-height="'30vh'"
					position="bottomLeft"
					v-click-out="togglePopup"
				>
					<div
						v-for="opt, i in monthOptions"
						:key="i"
						class="PopupMenu-item"
						@click="onSelectMonth(opt)"
					>
						{{ monthNames[opt] }}
					</div>
				</PopupMenu>
			</template>
			<template v-else>
				<span
					class="CalendarInputHeader-text"
					@click.stop="selectYearMonth"
				>{{ year }} {{ monthName | capitalize }}</span>
				<PopupMenu
					v-show="isPopup"
					:max-height="'30vh'"
					position="bottomLeft"
					v-click-outside="togglePopup"
				>
					<div
						v-for="opt, i in monthYearOptions"
						:key="i"
						class="PopupMenu-item"
						@click="onSelectYearMonth(opt)"
					>
						{{ opt.year }} {{ monthNames[opt.month] }}
					</div>
				</PopupMenu>
			</template>
		</div>
		<div class="CalendarInputHeader-buttons">
			<button
				class="CalendarInputHeader-button ChatIcon-parent"
				@click="prevMonth"
			>
				<CalendarIconUp />
			</button>
			<button
				class="CalendarInputHeader-button ChatIcon-parent"
				@click="nextMonth"
			>
				<CalendarIconDown />
			</button>
		</div>
	</div>
</template>

<script>
import { CalendarIconUp, CalendarIconDown } from './icons/icons.js'
import PopupMenu from '@ui/PopupMenu'
import { useYearOptions, useMonthOptions } from '@/composables/yearOptions'

export default {
	name: 'CalendarInputHeader',
	components: {
		CalendarIconUp,
		CalendarIconDown,
		PopupMenu,
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
	inject: [
		'getMonth',
		'getYear',
		'prevMonth',
		'nextMonth',
		'setMonth',
		'getStartYear',
		'getSeparateMonthYear',
	],
	data() {
		const now = new Date()
		const currentYear = now.getFullYear()
		const currentMonth = now.getMonth()

		const monthOptions = useMonthOptions()
		const monthOptionsReversed = useMonthOptions().reverse()

		return {
			isPopupMonth: false,
			isPopupYear: false,
			isPopup: false,
			monthYearOptions: useYearOptions().reverse().reduce((options, year) => {
				options.push(...monthOptionsReversed.reduce((result, month) => {
					if(year === currentYear && month > currentMonth) return result
					result.push({year, month})
					return result
				}, []))
				return options
			}, []),
			monthNames: monthOptions.map(month => this.$moment([0, month]).format('MMMM')),
		}
	},
	computed: {
		month(){
			return this.getMonth()
		},
		year(){
			return this.getYear()
		},
		monthName(){
			return this.$moment([this.year, this.month]).format('MMMM')
		},
		yearOptions(){
			return useYearOptions(this.getStartYear())
		},
		monthOptions(){
			return useMonthOptions()
		},
		separateMonthYear(){
			return this.getSeparateMonthYear()
		}
	},
	methods: {
		selectMonth(){
			setTimeout(() => {
				this.isPopupMonth = true
				this.isPopupYear = false
			}, 100)
		},
		selectYear(){
			this.isPopupYear = true
			this.isPopupMonth = false
		},
		selectYearMonth(){
			this.isPopup = true
		},
		onSelectMonth(month){
			this.setMonth(month)
			this.isPopupMonth = false
		},
		onSelectYear(year){
			this.setMonth(this.month, year)
			this.isPopupYear = false
		},
		onSelectYearMonth({year, month}){
			this.setMonth(month, year)
			this.isPopup = false
		},
		togglePopup(){
			this.isPopupMonth = false
			this.isPopupYear = false
			this.isPopup = false
		}
	},
	filters: {
		capitalize: function (value) {
			if (!value) return ''
			value = value.toString()
			return value.charAt(0).toUpperCase() + value.slice(1)
		},
	}
}
</script>

<style lang="scss">
	.CalendarInputHeader{
		display: flex;
		flex-flow: row nowrap;
		align-items: center;
		justify-content: space-between;
		gap: 1rem;
		margin-bottom: 1rem;
		position: relative;
		&-title{
			font-style: normal;
			font-weight: 500;
			font-size: 15px;
			line-height: 18px;
			letter-spacing: -0.03em;
			color: #122740;
		}
		&-text{
			cursor: pointer;
			user-select: none;
		}
		&-year,
		&-month{
			display: inline-block;
			padding: 2px 4px;
			border-radius: 4px;
			&:hover{
				background-color: #eee;
			}
		}
		&-buttons{
			display: flex;
			flex-flow: row nowrap;
			align-items: center;
			gap: 0.5rem;
		}
		&-button{
			display: block;
			border: none;
			background: transparent;
			&:focus{
				outline: none;
			}
		}
	}
</style>
