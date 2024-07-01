<template>
	<div class="check__item">
		<label class="check__item-label">
			<input
				class="check__item-checkbox"
				type="checkbox"
			>
			<p class="check__item-doit">Сделать {{ index }}</p>
		</label>
		<input
			:value="item.value"
			type="text"
			class="check__item-url"
			placeholder="URL"
		>
	</div>
</template>

<script>
export default {
	name: 'CheckListPopupItem',
	props: {
		item: {
			type: Object,
			required: true
		},
		index: {
			type: Number,
			required: true
		},
	},
}
</script>

<style lang="scss">
.check__item{
	display: flex;
	min-height: 4rem;
	height: 4rem;
	justify-content: space-between;
	align-items: center;
	margin-bottom: 2rem;
}
.check__item-label{
	display: flex;
	gap: 1.5rem;
	align-items: center;
	width: 100%;
	max-width: 12.5rem;
	height: 100%;
	margin-right: 8rem;
	color: #5b6166;
	font-size: 1.5rem;
}
.check__item-doit{
	white-space: nowrap;
}
.check__item-url{
	display: block;
	flex: 0 0 46rem;
	width: 100%;
	max-width: 100%;
	height: 100%;
	padding-left: 1.5rem;
	border: 1px solid #E7EAEA;
	border-radius: 1.5rem;
	font-size: 1.3rem;
	font-family: "Inter",sans-serif;
}

@media(max-width:650px){
	.check__item {
		display: block;
		height: auto;
	}
	.check__item-url{
		height: 4rem;
	}
}
</style>
