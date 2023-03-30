<template>
	<div class="CalendarInputHeader">
		<div class="CalendarInputHeader-title">
			{{ year }} {{ monthName | capitalize }}
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
export default {
	name: 'CalendarInputHeader',
	components: {
		CalendarIconUp,
		CalendarIconDown,
	},
	inject: [
		'getMonth',
		'getYear',
		'prevMonth',
		'nextMonth',
	],
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
	filters: {
		capitalize: function (value) {
			if (!value) return ''
			value = value.toString()
			return value.charAt(0).toUpperCase() + value.slice(1)
		}
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
		&-title{
			font-style: normal;
			font-weight: 500;
			font-size: 15px;
			line-height: 18px;
			letter-spacing: -0.03em;
			color: #122740;
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
