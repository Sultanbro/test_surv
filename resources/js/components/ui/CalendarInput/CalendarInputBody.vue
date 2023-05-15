<template>
	<div class="CalendarInputBody">
		<CalendarInputHeader />
		<CalendarInputMain v-if="!onlyMonth" />
		<CalendarInputFooter v-if="range" />
	</div>
</template>

<script>
import CalendarInputHeader from './CalendarInputHeader'
import CalendarInputMain from './CalendarInputMain'
import CalendarInputFooter from './CalendarInputFooter'
export default {
	name: 'CalendarInputBody',
	inject: ['getRange', 'getOnlyMonth'],
	components: {
		CalendarInputHeader,
		CalendarInputMain,
		CalendarInputFooter,
	},
	computed: {
		range(){
			return this.getRange()
		},
		onlyMonth(){
			return this.getOnlyMonth()
		}
	}
}
</script>

<style>
	.CalendarInputBody{
		display: flex;
		flex-flow: column nowrap;
		justify-content: space-between;
		gap: 1.2px;
		padding: 2rem;
	}
</style>
