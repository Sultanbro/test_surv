<template>
	<div
		v-if="items"
		class="BonusComments"
	>
		<template v-for="bonus, key in items">
			<div
				v-if="bonus.sum"
				:key="key"
				:title="bonus.sum"
				class="BonusComments-comment"
			>
				{{ bonus.comment }}
			</div>
		</template>
	</div>
</template>

<script>
export default {
	name: 'BonusComments',
	components: {},
	props: {
		items: {
			type: Array,
			default: () => []
		}
	}
}
</script>

<style lang="scss">
.BonusComments{
	&-comment{
		margin-bottom: 5px;
		cursor: default;
		&:hover{
			text-decoration: underline;
		}
	}
}
</style>
