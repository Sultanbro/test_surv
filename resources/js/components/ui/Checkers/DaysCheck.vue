<template>
	<div class="DaysCheck">
		<div
			v-for="day in 31"
			:key="day"
			class="DaysCheck-item ChatIcon-parent"
			:class="{'ChatIcon-active': value.includes(day)}"
			@click="onChange(day)"
		>
			<!-- ChatIconMassReaded -->
			<div class="DaysCheck-check">
				<ChatIconMassReaded v-if="value.includes(day)" />
			</div>
			<div class="DaysCheck-text">
				{{ day }}
			</div>
		</div>
		<img
			src="/images/dist/profit-info.svg"
			alt="info icon"
			class="img-info"
			v-b-popover.hover="'Если в месяце нет 31 числа то сообщение придет 30'"
		>
	</div>
</template>

<script>
import {
	ChatIconMassReaded,
} from '@icons'
export default {
	name: 'DaysCheck',
	components: {
		ChatIconMassReaded,
	},
	props: {
		value: {
			type: Array,
			default: () => []
		},
		max: {
			type: Number,
			default: 31
		}
	},
	methods: {
		onChange(key){
			const value = this.value.slice()
			const index = value.indexOf(key)
			if(~index){
				value.splice(index, 1)
			}
			else{
				value.push(key)
			}
			this.$emit('input', value)
		}
	}
}
</script>

<style lang="scss">
.DaysCheck{
	display: flex;
	flex-flow: row wrap;
	align-items: center;
	justify-content: flex-start;
	gap: 10px;

	&-item{
		display: flex;
		align-items: center;
		justify-content: flex-start;
		gap: 8px;

		padding: 4px;

		cursor: pointer;
		&:hover{
			background-color: #fff;
		}
	}
	&-check{
		display: flex;
		align-items: center;
		justify-content: center;

		width: 15px;
		height: 15px;
		border: 1px solid #777;
		border-radius: 3px;
	}
	&-text{
		width: 2em;
		font-weight: 600;
		font-size: 11px;
		line-height: 1.3;
		letter-spacing: -0.04em;

		color: #6181B8;
	}
	.img-info{
		width: 20px;
		vertical-align: middle;
	}
}
</style>
