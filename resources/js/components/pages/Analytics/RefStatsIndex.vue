<template>
	<div class="RefStatsIndex">
		<div class="RefStatsIndex-value">
			{{ value }}{{ unit }}
		</div>
		<div class="RefStatsIndex-label">
			{{ label }}
		</div>
	</div>
</template>

<script>
export default {
	name: 'RefStatsIndex',
	components: {},
	props: {
		value: {
			type: String,
			required: true
		},
		unit: {
			type: String,
			default: ''
		},
		label: {
			type: String,
			default: ''
		},
	},
}
</script>

<style lang="scss">
.RefStatsIndex{
	padding: 10px;
	border: 1px solid #E7EAEA;
	text-align: center;
	border-radius: 16px;
	&-value{
		margin-bottom: 16px;
		font-size: 36px;
	}
	&-label{
		font-size: 16px;
	}
}
</style>
