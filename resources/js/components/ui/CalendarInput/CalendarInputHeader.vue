<template>
	<div class="CalendarInputHeader">
		<div class="CalendarInputHeader-title">
			<span
				class="CalendarInputHeader-text"
				@click="selectMonth"
			>{{ year }} {{ monthName | capitalize }}</span>
			<PopupMenu
				v-show="isPopup"
				:max-height="'30vh'"
				position="bottomLeft"
				v-click-outside="togglePopupIfShown"
			>
				<div
					v-for="opt, i in monthOptions"
					:key="i"
					class="PopupMenu-item"
					@click="onSelectMonth(opt)"
				>
					{{ opt.year }} {{ monthNames[opt.month] }}
				</div>
			</PopupMenu>
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
	inject: [
		'getMonth',
		'getYear',
		'prevMonth',
		'nextMonth',
		'setMonth',
	],
	data() {
		const monthOptions = useMonthOptions()
		const monthOptionsReversed = useMonthOptions().reverse()
		return {
			isPopup: false,
			monthOptions: useYearOptions().reverse().reduce((options, year) => {
				options.push(...monthOptionsReversed.map(month => ({year, month})))
				return options
			}, []),
			monthNames: monthOptions.map(month => this.$moment([0, month]).format('MMMM'))
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
		}
	},
	methods: {
		selectMonth(){
			setTimeout(() => {
				this.isPopup = true
			}, 100)
		},
		onSelectMonth({month, year}){
			this.setMonth(month, year)
		},
		togglePopupIfShown(){
			if(!this.isPopup) return
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
