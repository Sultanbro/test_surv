<template>
	<div class="balance__item">
		<div class="balance__item-title">
			{{ title }}
		</div>
		<div class="balance__item-value">
			<slot />
		</div>
		<slot name="footer" />
	</div>
</template>

<script>
export default {
	name: 'BalanceItem',
	props: {
		title: {
			type: String,
			default: ''
		}
	}
}
</script>

<style lang="scss">
.balance__item{
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap:1rem;
    margin-bottom: 4rem;
    font-size:1.3rem;
    color:#62788B;
}
.balance__item-title{
    font-weight: 600;
}
</style>
