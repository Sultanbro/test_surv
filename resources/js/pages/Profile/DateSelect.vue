<template>
	<div
		class="DateSelect"
		:class="{'DateSelect_disabled': disabled}"
		@click.self="showDatePicker"
	>
		{{ yearPosition === 'before' ? $moment(value, 'DD.MM.YYYY').format('YYYY') : '' }}
		{{ capitalized($moment(value, 'DD.MM.YYYY').format('MMMM')) }}
		{{ yearPosition === 'after' ? $moment(value, 'DD.MM.YYYY').format('YYYY') : '' }}
		<i
			class="fa fa-chevron-down ml-a"
			@click.self="showDatePicker"
		/>
		<CalendarInput
			v-if="isDatePicker"
			:value="localValue"
			:open="isDatePicker"
			@close="onCloseDatePicker"
			@input="onInput"
			:only-month="true"
			:start-year="Math.max(new Date(user.created_at).getFullYear(), 2020)"
			:tabs="[]"
			popup
		/>
	</div>
</template>

<script>
import { mapGetters } from 'vuex'
import CalendarInput from '@ui/CalendarInput/CalendarInput'
const now = new Date()
const date = `${now.getDate()}.${now.getMonth() + 1}.${now.getFullYear()}`
export default {
	name: 'DateSelect',
	components: {
		CalendarInput,
	},
	props: {
		value: {
			type: String,
			default: date
		},
		disabled: {
			type: Boolean,
			default: false
		},
		yearPosition: {
			type: String,
			default: '' // before, after
		}
	},
	data(){
		return {
			isDatePicker: false,
			localValue: [this.value]
		}
	},
	computed: {
		...mapGetters(['user']),
	},
	watch: {
		value(){
			this.localValue = [this.value]
		},
	},
	methods: {
		onInput(value){
			this.$emit('input', value[0])
			// this.isDatePicker = false
		},
		capitalized(str) {
			const capitalizedFirst = str[0].toUpperCase();
			const rest = str.slice(1);

			return capitalizedFirst + rest;
		},
		showDatePicker(){
			if(this.disabled) return
			this.isDatePicker = !this.isDatePicker
		},
		onCloseDatePicker(e){
			if(this.$el.contains(e.target)) return
			this.isDatePicker = false
		},
	}
}
</script>

<style lang="scss">
.DateSelect{
	display: flex;
	flex-flow: row nowrap;
	align-items: center;
	justify-content: flex-start;
	gap: 1rem;

	padding: 1.5rem 2rem;
	border: 1px solid #E7EAEA;
	border-radius: 1.5rem;

	position: relative;

	font-size: 1.4rem;
	line-height: 1;
	color: #62788B;

	background-color: #fff;
	cursor: pointer;

	.CalendarInput{
		min-width: 100%;
		margin-left: -1rem;
		&-content{
			min-width: 100%;
		}
	}
	.CalendarInputBody{
		flex: 1;
	}
	.CalendarInputHeader{
		margin-bottom: 0;
	}

	&_disabled{
		background-color: #eee;
		cursor: default;
	}
}
</style>
