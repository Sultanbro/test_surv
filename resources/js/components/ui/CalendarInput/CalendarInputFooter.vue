<template>
	<div class="CalendarInputFooter">
		<input
			type="text"
			readonly
			:value="minDateText"
			size="10"
			class="CalendarInputFooter-input"
		>
		&mdash;
		<input
			type="text"
			readonly
			:value="maxDateText"
			size="10"
			class="CalendarInputFooter-input"
		>
	</div>
</template>

<script>
export default {
	name: 'CalendarInputFooter',
	inject: ['getTSValue', 'getFormat'],
	computed: {
		format(){
			return this.getFormat()
		},
		tsValue(){
			return this.getTSValue()
		},
		minDate(){
			return Math.min(...this.tsValue)
		},
		maxDate(){
			return Math.max(...this.tsValue)
		},
		minDateText(){
			return this.$moment(this.minDate).format(this.format)
		},
		maxDateText(){
			return this.$moment(this.maxDate).format(this.format)
		}
	}
}
</script>

<style lang="scss">
.CalendarInputFooter{
	display: flex;
	justify-content: center;
	align-items: center;
	gap: 1rem;
	padding-top: 1.5rem;
	margin-top: auto;
	border-top: 0.5px solid #BFD2F3;
	&-input{
		padding: 1.3rem 2.5rem;
		border: 1px solid #CED9E8;
		border-radius: 5px;

		font-weight: 500;
		font-size: 11px;
		line-height: 14px;
		color: #4E6387;
		letter-spacing: -0.03em;
		text-align: center;

		background-color: #fff;
	}
}
</style>
