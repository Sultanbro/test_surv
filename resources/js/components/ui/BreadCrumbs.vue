<template>
	<ul class="BreadCrumbs">
		<li
			v-for="item, index in items"
			:key="index"
			class="BreadCrumbs-item"
		>
			<i
				v-if="index"
				class="BreadCrumbs-delimiter fa fa-chevron-right"
			/>
			<a
				v-if="item.link"
				:href="item.link"
				class="BreadCrumbs-link"
			>
				{{ item.title }}
			</a>
			<router-link
				v-else-if="item.to"
				:to="item.to"
				class="BreadCrumbs-link"
			>
				{{ item.title }}
			</router-link>
			<template v-else>
				{{ item.title }}
			</template>
		</li>
	</ul>
</template>
<script>
export default {
	name: 'BreadCrumbs',
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
.BreadCrumbs{
	display: flex;
	flex-flow: row wrap;
	align-items: center;
	gap: 10px;

	padding: 0;
	margin: 0;
	list-style: none;

	&-item{
		display: flex;
		flex-flow: row nowrap;
		align-items: center;
		gap: 10px;

		padding: 0;
		margin: 0;

		font-size: 12px;
		color: #858585;

		list-style: none;

		&:last-child{
			color: #000;
		}
	}
	&-link{
		color: inherit;
		text-decoration: none;
		&:hover{
			text-decoration: underline;
		}
	}
	&-delimiter{
		font-size: 8px;
		color: #ccc;
	}
}
</style>
