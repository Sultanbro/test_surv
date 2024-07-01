<template>
	<div
		v-if="items.length"
		class="AuthMethod"
		:style="`--items-count: ${items.length};`"
	>
		<div
			v-for="item in items"
			:key="item.value"
			class="AuthMethod-item"
			:class="{
				'AuthMethod-item_selected': value === item.value,
				'AuthMethod-item_disabled': item.disabled,
			}"
			@click="onClick(item)"
		>
			{{ item.title }}
		</div>
	</div>
</template>

<script>
export default {
	name: 'AuthMethod',
	components: {},
	props: {
		value: {
			type: String,
			default: '',
		},
		items: {
			type: Array,
			default: () => []
		},
	},
	data(){
		return {}
	},
	computed: {},
	watch: {},
	created(){},
	mounted(){},
	beforeDestroy(){},
	methods: {
		onClick(item){
			if(item.disabled) return
			this.$emit('input', item.value)
		}
	},
}
</script>

<style lang="scss">
.AuthMethod{
	display: flex;
	flex-flow: row nowrap;

	padding: 4px;

	background: #F2F2F2;
	border-radius: 16px;

	&-item{
		flex: 0 0 calc(100% / var(--items-count, 2));
		display: flex;
		align-items: center;
		justify-content: center;

		height: 56px;

		font-size: 16px;
		font-weight: 400;
		line-height: 24px;
		text-align: center;
		color: #333;

		border-radius: 12px;
		cursor: pointer;
		user-select: none;

		&:hover{
			color: #1E40AF;
		}

		&_disabled{
			cursor: not-allowed;
			color: #666;
			&:hover{
				color: #666;
			}
		}

		&_selected{
			color: #1E40AF;
			background-color: #fff;
			cursor: default;
		}
	}
}
</style>
