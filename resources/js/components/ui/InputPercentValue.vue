<template>
	<span class="InputPercentValue">
		<span
			class="InputPercentValue-percent"
			:class="{'InputPercentValue-selected': value}"
			title="Процент"
			@click="$emit('input', 1)"
		>%</span>
		<span
			class="InputPercentValue-value"
			:class="{'InputPercentValue-selected': !value}"
			title="Сумма"
			@click="$emit('input', 0)"
		>000</span>
	</span>
</template>

<script>
export default {
	name: 'InputPercentValue',
	components: {},
	props: {
		value: {
			type: Number,
			default: 0,
		}
	},
	data(){
		return {}
	},
	computed: {},
	watch: {},
	created(){},
	mounted(){},
	beforeDestroy(){},
	methods: {},
}
</script>

<style lang="scss">
.InputPercentValue{
	display: inline-flex;
	flex-flow: column nowrap;
	align-items: stretch;

	user-select: none;
	cursor: pointer;

	&-percent{
		padding: 2px;
		font-size: 14px;
		text-align: center;
		background-color: #F7FAFC;
	}
	&-value{
		padding: 2px;
		font-size: 12px;
		text-align: center;
		background-color: #F7FAFC;
	}
	&-selected{
		color: #fff;
		background: #3361FF;
	}
}
</style>
