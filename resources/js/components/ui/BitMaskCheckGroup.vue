<template>
	<div
		class="BitMaskCheckGroup"
		:class="{
			'BitMaskCheckGroup_red': red
		}"
	>
		<div
			v-for="option in options"
			:key="option.value"
			class="BitMaskCheckGroup-item"
			:class="{
				'BitMaskCheckGroup-item_checked': value & option.value
			}"
			@click="onChange(option.value)"
		>
			{{ option.title }}
		</div>
	</div>
</template>

<script>

export default {
	name: 'BitMaskCheckGroup',
	components: {},
	props: {
		value: {
			type: Number,
			default: 0
		},
		options: {
			type: Array,
			default: () => [
				{
					value: 0b0000001,
					title: 'Пн'
				},
				{
					value: 0b0000010,
					title: 'Вт'
				},
				{
					value: 0b0000100,
					title: 'Ср'
				},
				{
					value: 0b0001000,
					title: 'Чт'
				},
				{
					value: 0b0010000,
					title: 'Пт'
				},
				{
					value: 0b0100000,
					title: 'Сб'
				},
				{
					value: 0b1000000,
					title: 'Вс'
				},
			]
		},
		red: {
			type: Boolean
		}
	},
	methods: {
		onChange(value){
			this.$emit('input', this.value ^ value)
		}
	}
}
</script>

<style lang="scss">
$BitMaskCheckGroup-color: #8DA0C1;
.BitMaskCheckGroup{
	display: flex;
	flex-flow: row nowrap;
	&_red{
		.BitMaskCheckGroup{
			&-item{
				&_checked{
					background-color: #F6264C;
					&:hover{
						background-color: #F6264C;
					}
				}
			}
		}
	}
	&-item{
		display: flex;
		align-items: center;
		justify-content: space-between;
		gap: 1rem;

		padding: 1.3rem;
		border: 1px solid $BitMaskCheckGroup-color;
		border-left: none;

		font-size: 1.2rem;
		color: $BitMaskCheckGroup-color;
		background-color: #fff;
		cursor: pointer;

		&:first-child{
			border-left: 1px solid $BitMaskCheckGroup-color;
			border-radius: .5rem 0 0 .5rem;
		}
		&:last-child{
			border-radius: 0 .5rem .5rem 0;
		}
		&:hover{
			background-color: #EDF6FF;
		}
		&_checked{
			background-color: #3361FF;
			color: #fff;
			&:hover{
				background-color: #3361FF;
				color: #fff;
			}
		}
	}
}
</style>
