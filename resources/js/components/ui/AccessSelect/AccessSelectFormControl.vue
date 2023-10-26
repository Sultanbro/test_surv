<template>
	<div
		class="AccessSelectFormControl"
		@click="$emit('click', $event)"
	>
		<slot v-if="items.length">
			<b-badge
				v-for="item, index in items"
				:key="index"
				class="AccessSelectFormControl-item"
				:class="[`AccessSelectFormControl-item${item.type}`]"
			>
				{{ item.name }}
			</b-badge>
		</slot>
		<slot
			v-else
			name="placeholder"
		>
			{{ placeholder }}
		</slot>
	</div>
</template>

<script>
export default {
	name: 'AccessSelectFormControl',
	components: {},
	props: {
		items: {
			type: Array,
			default: () => []
		},
		placeholder: {
			type: String,
			default: ''
		},
	}
}
</script>

<style lang="scss">
.AccessSelectFormControl{
	display: flex;
	flex-flow: row wrap;
	align-items: center;
	justify-content: flex-start;
	gap: 5px;

	width: 100%;
	min-height: 35px;
	padding: 5px 20px;
	border: 1px solid #e8e8e8;

	font-size: 14px;
	line-height: 1.3;

	border-radius: 6px;
	background-color: #F7FAFC;

	&-item{
		user-select: none;
		color: #333;
	}
	&-item0{
		background-color: #f89fa4;
	}
	&-item1{
		background-color: #b5d5e5;
	}
	&-item2{
		background-color: #e5e49b;
	}
	&-item3{
		background-color: #80d593;
	}
}
</style>
