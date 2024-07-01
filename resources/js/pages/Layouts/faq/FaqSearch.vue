<template>
	<div class="FaqSearch">
		<div
			v-for="item in items"
			:key="item.id"
			class="FaqSearch-item"
			:class="{'FaqSearch-item_active': item.id === activeItemId}"
		>
			<p
				class="FaqSearch-title"
				@click="onSelect(item)"
			>
				{{ item.title }}
			</p>
		</div>
	</div>
</template>

<script>
export default {
	name: 'FaqSearch',
	components: {},
	props: {
		active: {
			type: Object,
			default: null
		},
		items: {
			type: Array,
			default: () => []
		}
	},
	data(){
		return {}
	},
	computed: {
		activeItemId(){
			return this.active?.id || 0
		},
	},
	watch: {},
	created(){},
	mounted(){},
	beforeDestroy(){},
	methods: {
		onSelect(item){
			this.$emit('select', item)
		},
	},
}
</script>

<style lang="scss">
.FaqSearch{
	&-item {
		margin-top: 6px;
		&_active{
			.FaqSearch{
				&-title{
					background-color: rgba(96, 142, 233, 0.2);
					color: #333333;
				}
			}
		}
	}
	&-title {
		display: flex;
		align-items: center;
		justify-content: space-between;

		height: 40px;
		padding: 0 20px;

		font-size: 16px;

		cursor: pointer;
		border-radius: 20px 0 0 20px;
	}
}
</style>
