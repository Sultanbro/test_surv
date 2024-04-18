<template functional>
	<div
		v-bind="data.attrs"
		:class="['AuthLangItem', data.staticClass]"
		v-on="data.nativeOn"
	>
		<img
			:src="props.item.flag"
			alt=""
			class="AuthLangItem-flag"
		>
		<span class="AuthLangItem-label">
			{{ props.item.label }}
		</span>
		<span class="AuthLangItem-icon">
			<slot name="icon" />
		</span>
	</div>
</template>

<script>
export default {
	name: 'AuthLangItem',
	props: {
		item: {
			type: Object,
			default: () => ({}),
		},
	},
}
</script>

<style lang="scss">
.AuthLangItem{
	display: flex;
	align-items: center;
	gap: 6px;

	padding: 4px 12px;
	cursor: pointer;
	user-select: none;

	&-flag{
		display: block;
		width: 21px;
		border-radius: 2px;
	}
	&-label{
		font-size: 16px;
		font-weight: 400;
		line-height: 24px;
		text-align: left;
	}

	&-icon{
		width: 16px;
		height: 16px;
		margin-inline: 2px 4px;
		transition: 0.3s;
	}

	&:hover{
		color: #0C50FF;
		.AuthIcon-fill{
			fill: #0C50FF;
		}
	}
}
</style>
