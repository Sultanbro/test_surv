<template>
	<div class="ProgressBar">
		<div
			class="ProgressBar-bar"
			:class="['ProgressBar-bar_' + color]"
			:style="`width: ${progress};`"
		/>
	</div>
</template>

<script>
export default {
	name: 'ProgressBar',
	components: {},
	props: {
		progress: {
			type: String,
			default: '0%'
		},
		color: {
			type: String,
			default: 'green'
		}
	}
}
</script>

<style lang="scss">
.ProgressBar{
	height: 10px;

	border-radius: 999rem;
	background: linear-gradient(243.47deg, #F1F1F5 33.3%, #E9F2FF 100%);
	&-bar{
		min-width: 20px;
		max-width: 100%;
		height: 10px;
		border-radius: 999rem;

		&_green{
			background: linear-gradient(270deg, #67F1C8 0%, #24E795 100%);
		}
		&_blue{
			background: linear-gradient(270deg, #67C8F1 0%, #2495E7 100%);
		}
		&_yellow{
			background: linear-gradient(270deg, #C8F167 0%, #95E724 100%);
		}
	}
}
</style>
